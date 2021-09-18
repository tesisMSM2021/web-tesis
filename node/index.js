var mysql = require('mysql');
var mqtt = require('mqtt');

//CREDENCIALES MYSQL
var con = mysql.createConnection({
  host:'your-domain',
  user: 'your-user-dataBase',
  password: 'your-user-pass-dataBase',
  dataBase: 'your-database-name'
});

//CREDENCIALES MQTT
var options = {
  port: 1883,
  host: 'your-host',
  clientId: 'acces_control_server_' + Math.round(Math.random() * (0- 10000) * -1) ,
  username: 'your_EMQX_user',
  password: 'your_EMQX_pass',
  keepalive: 60,
  reconnectPeriod: 1000,
  protocolId: 'MQIsdp',
  protocolVersion: 3,
  clean: true,
  encoding: 'utf8'
};

var client = mqtt.connect('your_emqx_url', options);

//SE REALIZA LA CONEXION
client.on('connect', function () {
  console.log("Conexión  MQTT Exitosa!");
  client.subscribe('+/#', function (err) {
    console.log("Subscripción exitosa!")
  });
})

//recibir mensajes de values temperatura, humedad, humedad de suelo
client.on('message', function (topic, message) {
  console.log("Mensaje recibido desde -> " + topic + " Mensaje -> " + message.toString());
  if (topic == "values"){
    var msg = message.toString();
    var sp = msg.split(",");
    var temp = sp[0];
    var hum = sp[1];
    var soilmoisturepercent = sp[2];

    //hacemos la consulta para insertar....

    var query = "INSERT INTO `your_database`.`data` (`data_temp`, `data_hum`, `data_hum_soil`) VALUES (" + temp + ", " + hum + ", " + soilmoisturepercent + ")";
    con.query(query, function (err, result, fields) {
      if (err) throw err;
      console.log("Fila de valores de sensores insertada correctamente");
    });
  }
});

//recibir mensajes de estados de los actuadores
client.on('message', function (topic, message) {
  console.log("Mensaje recibido desde -> " + topic + " Mensaje -> " + message.toString());
  if (topic == "valuesStates"){
    var msg = message.toString();
    var sp = msg.split(",");
    var state_window = sp[0];
    var state_cooler = sp[1];
    var state_pump = sp[2];
    var state_manual = sp[3];

    //hacemos la consulta para insertar....

    var query = "INSERT INTO `your_database`.`states` (`state_window`, `state_cooler`, `state_pump`, `state_manual`) VALUES (" + state_window + ", " + state_cooler + ", " + state_pump + ", " + state_pump +")";
    con.query(query, function (err, result, fields) {
      if (err) throw err;
      console.log("Fila insertada de estados correctamente");
    });
  }
});.

client.on('message', function (topic, message) {
  console.log("Mensaje recibido desde -> " + topic + " Mensaje -> " + message.toString());
  if (topic == "valuesStatesManual"){
    var msg = message.toString();
    var sp = msg.split(",");
    var state_manual_ventana = sp[0];
    var state_manual_cooler = sp[1];
    var state_manual_bomba = sp[2];

    //hacemos la consulta para insertar....

    var query = "INSERT INTO `your_database`.`state_manual` (`state_manual_ventana`, `state_manual_cooler`, `state_manual_bomba` ) VALUES ("+ state_manual_ventana + ", " + state_manual_cooler + ", " + state_manual_bomba + ")";
    con.query(query, function (err, result, fields) {
      if (err) throw err;
      console.log("Fila insertada de estados manuales correctamente");
    });
  }
});

//para mantener la sesión con mysql abierta
setInterval(function () {
  var query ='SELECT 1 + 1 as result';

  con.query(query, function (err, result, fields) {
    if (err) throw err;
  });

}, 5000);
