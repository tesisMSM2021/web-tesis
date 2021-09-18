#include<Arduino.h>
#include <WiFi.h> // libreria para conexion a wifi
#include <PubSubClient.h> //libreria para administrar topicos y protocolo mqtt
#include "DHT.h" // libreria para administrar sensor dht11
#include <ESP32Servo.h> //libreria para activar el servomotor
#include <iostream> // libreria input/output
#include <ctime>
#include <string>

//constantes conexion wifi
const char* ssid = "your-ssid";
const char* password = "your-pass";

//constantes conexion broker mqtt
const char* mqtt_server = "your-domain-mqtt-server";
const int mqtt_port = 1883;

//constantes conexion DB
const char* mqtt_user = "your-emqx-user";
const char* mqtt_pass = "your-emqx-pass";

//variables globales
bool state_manual = false;
bool state_manual_ventana = false;
bool state_manual_cooler = false;
bool state_manual_bomba = false;
int secsIn = 0; // variable para conservar el timestamp de cuando se envía la solicitud de que entre en modo manual
int secsInVentana = 0;
int secsInVentilador = 0;
int secsInBomba = 0;
int secsOut = 0; // variable para ir llevando medida del tiempo que pasó. Guarda el timestamp actual.
int intervalo = 15; // Intervalo de manual;

//variables para crear mensaje y enviar por mqtt
long lastMsg = 0;
char msg[25];
long lastMsgStates = 0;
char msgStates[25];
long lastMsgManual = 0;
char msgStatesManual[25];

//Variables sensor de temperatura
int temp = 0;
int hum = 0;
int maxTemp = 27;
int maxHum = 70;
int delayDHT11 = 1000;

//Variables y constantes sistema de riego automatizado
const int AirValue = 3600;   //valor del sensor totalmente seco
const int WaterValue = 1450;  //valor del sensor totalmente humedo
int soilMoistureValue = 0;
int soilmoisturepercent=0;
int delaySoilSensor = 1000;
int pinBomba = 18;
bool state_pump;

//variables servomotor
int pos = 0;
int servoPin = 13;
int delayServo = 15;
bool state_window;

//variables cooler
int pinCooler = 15;
bool state_cooler;

//creacion de objetos
WiFiClient espClient;
PubSubClient client(espClient);
#define DHTPIN 4
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);
Servo myservo;

// prototipos de las funciones
void setup_wifi();
void callback(char* topic, byte* payload, unsigned int length);
void reconnect();

//////////////////////////////////////////funciones actuadores para logica automatizada

void moverVentana(int op) {
  	if(op == 1) {
      myservo.write(0);
  		state_window = false;
  	}
  	else {
      myservo.write(90);
  		Serial.println("Ventana abierta");
  		state_window = true;

  	}
}

void operarBomba(int op) {
  	if(op == 1) {
  		Serial.println("seco, Prender bomba");
  		digitalWrite(pinBomba, HIGH); // el pin 18 activa la alimentacion de la bomba
  		state_pump = true;
  	}
  	else {
  		Serial.println("inundado, Apagar bomba");
  		digitalWrite(pinBomba, LOW); // el pin 18 corta la alimentacion de la bomba
  		state_pump = false;
  	}
}

void operarVentilador(int op) {
  	if(op == 1) {
  		Serial.println("cooler prendido");
  		digitalWrite(pinCooler, LOW);
  		state_cooler = true;
  	}
  	else {
  		Serial.println("cooler apagado");
  		digitalWrite(pinCooler, HIGH);
  		state_cooler = false;
  	}
}

////////////////////////////////////////// fin funciones actuadores automatizados

//////////////////////////////////////////funciones actuadores para logica manual

void moverVentanaManual(int op) {
  	state_manual_ventana = true;
  	secsInVentana = time(NULL);
	  if(op == 1) {
      	myservo.write(0);
      	Serial.println(myservo.read());
  		Serial.println("ventana cerrada manualmente");
      	state_window = false;


  	}
  	else {
  		Serial.println("Ventana abierta");
     	 myservo.	write(90);
  		Serial.println("Ventana abierta manualmente");
     	state_window = true;
   	}
}

void operarBombaManual(int op) {
	secsInBomba = time(NULL);
	state_manual_bomba = true;
	if(op == 1) {
  		Serial.println("seco, Prender bomba manualmente");
  		digitalWrite(pinBomba, HIGH); // el pin 18 activa la alimentacion de la bomba
      	state_pump = true;

  	}
  	else {
  		Serial.println("inundado, Apagar bomba manualmente");
  		digitalWrite(pinBomba, LOW); // el pin 18 corta la alimentacion de la bomba
     	state_pump = false; // Esto estaba cruzado

    }
}

void operarVentiladorManual(int op) {
	secsInVentilador = time(NULL);
  	state_manual_cooler = true;
	  if(op == 1) {
  		Serial.println("cooler prendido manualmente");
  		digitalWrite(pinCooler, LOW);
      	state_cooler = true;

  	}
  	else {
  		Serial.println("cooler apagado manualmente");
  		digitalWrite(pinCooler, HIGH);
     	state_cooler = false;

  	}
}

/////////////////////////////////////// fin funciones actuadores para logica manual

void setup() {
	setup_wifi();
   	client.setServer(mqtt_server, mqtt_port);
   	client.setCallback(callback);
   	Serial.println(F("DHTxx test!"));
   	dht.begin();
	pinMode(pinBomba, OUTPUT); // pin de salida del relay para la bomba de agua.
   	pinMode(pinCooler, OUTPUT); // pin de salida para el cooler
	myservo.attach(servoPin); //salida servo
   	Serial.begin(9600);
}

void loop() {
	//// Acá en el loop se controla si se venció el tiempo de actividad
	secsOut = time(NULL);
	if(secsOut-secsInVentana > intervalo && secsInVentana != 0) {
		state_manual_ventana = false;
		secsInVentana = 0;
	}
	if(secsOut-secsInVentilador > intervalo && secsInVentilador != 0) {
		state_manual_cooler = false;
		secsInVentilador = 0;
	}
	if(secsOut-secsInBomba > intervalo && secsInBomba != 0) {
		state_manual_bomba = false;
		secsInBomba = 0;
	}

	if (!client.connected()) {
		reconnect();
	}

	client.loop();
	//medicion temperatura y humedad ambiente sensor dht11
	delay(delayDHT11);
	float temp = dht.readTemperature();
	delay(delayDHT11);
	float hum = dht.readHumidity();

	if (isnan(temp) || isnan(hum)) {
		Serial.println(F("Fallo el sensor DHT11"));
		return;
	}

	//salida por serial lecturas de temp y hum
	Serial.println(F("Temperatura: "));
	Serial.println(temp);
	Serial.println(F("Humedad: "));
	Serial.println(hum);

  //medicion humedad de suelo
  soilMoistureValue = analogRead(35);  //lectura del sensor de hum de suelo.
  Serial.println(soilMoistureValue);
  soilmoisturepercent = map(soilMoistureValue, AirValue, WaterValue, 0, 100);
  if(soilmoisturepercent >= 100) {
    Serial.println("100 %");
  }
  else if(soilmoisturepercent <=0) {
    Serial.println("0 %");
  }

  else if(soilmoisturepercent >0 && soilmoisturepercent < 100) {
    Serial.print(soilmoisturepercent);
    Serial.println("%");
  }
  delay(delaySoilSensor);

	  ///////////logica para automatizacion dentro del loop()
	if(state_manual_ventana == false) {
	  	if(temp >= 30) {
	      moverVentana(0);


	  	} else if ((temp >= -1 && temp <= 29)) {

	      moverVentana(1);

	  	}
	}

	if(state_manual_bomba == false) {
	    if (soilmoisturepercent <= 50) {

	        operarBomba(1);
		}

		else if (soilmoisturepercent >= 65) {

	      operarBomba(0);

		}
	}

	if(state_manual_cooler == false) {
	    if ((hum > 31 && hum <= 75)) { // Este if tenía una condición equivocada, tomaba mayor o igual y eso se pisaba con el de abajo

	      operarVentilador(1);

	    }
		else if ((hum >= 0 && hum <= 30) || (hum > 75 && hum <= 100)) {

	      operarVentilador(0);

	    }
	}

	/////////// fin logica para automatizacion

	//armado del mensaje y publicacion al topico values para recibirlos en el broker mqtt
	long now = millis();
	if (now - lastMsg > 500){
			lastMsg = now;
			String to_send = String(temp) + "," + String(hum) + "," + String(soilmoisturepercent);
			to_send.toCharArray(msg, 25);
			Serial.print("Publicamos mensaje valores de hum, temp, hum soil  -> ");
			Serial.println(msg);
			client.publish("values", msg);
	}

	//armado del mensaje y publicacion al topico valuesState para recibirlos en el broker mqtt
	long nowState = millis();
	if (nowState - lastMsgStates > 500){
			lastMsgStates = nowState;
			String to_send = String(state_window) + "," + String(state_cooler) + "," + String(state_pump);
			to_send.toCharArray(msgStates, 25);
	    Serial.print("Publicamos mensaje valores de estados automaticos ventana, cooler, bomba  -> ");
			Serial.println(msgStates);
			client.publish("valuesStates", msgStates);
	}

	//armado del mensaje y publicacion al topico valuesStatemanual para recibir en el broqker mqtt
	long nowStateManual = millis();
	if (nowStateManual - lastMsgManual > 500){
			lastMsgManual = nowStateManual;
			String to_send = String(state_manual_ventana) + "," + String(state_manual_cooler) + "," + String(state_manual_bomba);
			to_send.toCharArray(msgStatesManual, 25);
	    Serial.print("Publicamos mensaje valores de estados manuales ventana, cooler, bomba  -> ");
			Serial.println(msgStatesManual);
			client.publish("valuesStatesManual", msgStatesManual);
		}
	}

//funciones de conexion y reconexion wifi
void setup_wifi(){
  	delay(10);
  	Serial.println();
  	Serial.print("Conectando a "); 	// Nos conectamos a nuestra red Wifi
  	Serial.println(ssid);

  	WiFi.begin(ssid, password);

  	while (WiFi.status() != WL_CONNECTED) {
  		delay(500);
  		Serial.print(".");
  	}

  	Serial.println("");
  	Serial.println("Conectado a red WiFi!");
  	Serial.println("Dirección IP: ");
  	Serial.println(WiFi.localIP());
}

void callback(char* topic, byte* payload, unsigned int length){
  	String incoming = "";
  	Serial.print("Mensaje recibido desde -> ");
  	Serial.print(topic);
  	Serial.println("");
  	for (int i = 0; i < length; i++) {
  		incoming += (char)payload[i];
  	}
  	incoming.trim();
  	Serial.println("Mensaje -> " + incoming);

  	//activacion de actuadores de forma manual

    if (incoming == "ventana abierta") {

        moverVentanaManual(0);
  	} else if (incoming == "ventana cerrada") {
        moverVentanaManual(1);
    }

  	if (incoming == "bomba encendida") {
  		 // El momento donde arranca manual
        operarBombaManual(1);
  	}else if(incoming == "bomba apagada"){
        operarBombaManual(0);
    }

    if ( incoming == "ventilacion encendida") {

        operarVentiladorManual(1);
  	} else if(incoming == "ventilacion apagada"){
        operarVentiladorManual(0);
    }
}

void reconnect() {
	while (!client.connected()) {
		Serial.print("Intentando conexión Mqtt...");
		// Creamos un cliente ID
		String clientId = "esp32_";
		clientId += String(random(0xffff), HEX);
		// coneccion
		if (client.connect(clientId.c_str(),mqtt_user,mqtt_pass)) {
			Serial.println("Conectado!");

			// Nos suscribimos al topico
			client.subscribe("dispositivoServo");
			client.subscribe("dispositivoBomba");
			client.subscribe("dispositivoCooler");
			state_manual = true;
		} else {
			Serial.print("falló :( con error -> ");
			Serial.print(client.state());
			Serial.println(" Intentamos de nuevo en 5 segundos");
			delay(5000);
		}
	}
}
