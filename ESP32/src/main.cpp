#include<Arduino.h>
#include <WiFi.h> // libreria para conexion a wifi
#include <PubSubClient.h> //librria para administrar topicos y protocolo mqtt
#include "DHT.h" // libreria para administrar sensor dht11
#include <ESP32Servo.h> //libreria para activar el servomotor

//constantes conexion wifi
const char* ssid = "miguelangel";
const char* password = "jesus1778";

//constantes conexion broker mqtt
const char* mqtt_server = "tesismsm2021.ga";
const int mqtt_port = 1883;

//constantes conexion DB
const char* mqtt_user = "web_client";
const char* mqtt_pass = "tesis_2021";

//variables para crear mensaje y enviar por mqtt
long lastMsg = 0;
char msg[25];
long lastMsgStates = 0;
char msgStates[25];

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
boolean state_pump;

//variables servomotor
int pos = 0;
int servoPin = 13;
int delayServo = 15;
boolean state_window;

//variables cooler
int pinCooler = 15;
boolean state_cooler;

//creacion de objetos
WiFiClient espClient;
PubSubClient client(espClient);
#define DHTPIN 4
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);
Servo myservo;

//Llamadas a funciones
void setup_wifi();
void callback(char* topic, byte* payload, unsigned int length);
void reconnect();

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

	// apertura  y cierre de la ventana con el servo motor
	if(temp > 27 ) {
			for (pos = 90; pos >= 0; pos -= 1 ) {
			myservo.write(0);
				delay(15);
			}
			Serial.println("ventana abierta");
			state_window = true;

	} else {
			for (pos = 90; pos <= 90; pos += 1) {
			myservo.write(90);
				delay(15);
		 }
		 Serial.println("Ventana cerrada");
		 state_window = false;
	}

  //medicion humedad de suelo
  soilMoistureValue = analogRead(35);  //lectura del sensor de hum de suelo.
  Serial.println(soilMoistureValue);
  soilmoisturepercent = map(soilMoistureValue, AirValue, WaterValue, 0, 100);

  if (soilmoisturepercent < 40) { //porcentaje de hS para apagar la bomba
      Serial.println("seco, Prender bomba");
      digitalWrite(pinBomba, HIGH); // el pin 18 activa la alimentacion de la bomba
      state_pump = true;
  }
  else if (soilmoisturepercent > 50) { //porcentaje de hSpara prender la bomba
      Serial.println("mojado, Apagar bomba");
      digitalWrite(pinBomba, LOW); // el pin 18 corta la alimentacion de la bomba
      state_pump = false;
  }

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

	//Prendido apagado del cooler segun la temperatura y humedad
	if (hum < 60) {
			Serial.println("cooler apagado");
	    digitalWrite(pinCooler, HIGH);
	    state_cooler = false;
	} else {
			Serial.println("cooler prendido");
			digitalWrite(pinCooler, LOW);
			state_cooler = true;
	}

	//armado del mensaje y publicacion al topico values para recibirlos en el broker mqtt
	long now = millis();
	if (now - lastMsg > 500){
		lastMsg = now;
		String to_send = String(temp) + "," + String(hum) + "," + String(soilmoisturepercent);
		to_send.toCharArray(msg, 25);
		Serial.print("Publicamos mensaje valores  -> ");
		Serial.println(msg);
		client.publish("values", msg);
	}

	//armado del mensaje y publicacion al topico valuesState para recibirlos en el broker mqtt
	long nowState = millis();
	if (nowState - lastMsgStates > 500){
		lastMsgStates = nowState;
		String to_send = String(state_window) + "," + String(state_cooler) + "," + String(state_pump);
		to_send.toCharArray(msgStates, 25);
		Serial.print("Publicamos mensaje estados -> ");
		Serial.println(msgStates);
		client.publish("valuesStates", msgStates);
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

	if ( incoming == "bomba encendida") {
		digitalWrite(pinBomba, HIGH);
		state_pump = true;
	} else {
		digitalWrite(pinBomba, LOW);
		state_pump = false;
	}

  if ( incoming == "ventilacion encendida") {
		digitalWrite(pinCooler, LOW);
		state_cooler = true;
	} else {
		digitalWrite(pinCooler, HIGH);
		state_cooler = false;
	}

	if ( incoming == "ventana abierta") {
			for (pos = 90; pos >= 0; pos -= 1 ) {
				myservo.write(0);
				delay(15);
			}
			state_window = true;
	} else {
			for (pos = 90; pos <= 90; pos += 1) {
				myservo.write(90);
				delay(15);
		 }
		 state_window = false;
	}
}

void reconnect() {
	while (!client.connected()) {
		Serial.print("Intentando conexión Mqtt...");
		// Creamos un cliente ID
		String clientId = "esp32_";
		clientId += String(random(0xffff), HEX);
		// Intentamos conectar
		if (client.connect(clientId.c_str(),mqtt_user,mqtt_pass)) {
			Serial.println("Conectado!");
			// Nos suscribimos al topico
			client.subscribe("dispositivoServo");
			client.subscribe("dispositivoBomba");
			client.subscribe("dispositivoCooler");
		} else {
			Serial.print("falló :( con error -> ");
			Serial.print(client.state());
			Serial.println(" Intentamos de nuevo en 5 segundos");

			delay(5000);
		}
	}
}
