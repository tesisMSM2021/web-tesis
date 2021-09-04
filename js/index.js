/*
******************************
****** CONEXION *************
******************************
*/
const options = {
  clean: true, // retain session
  connectTimeout: 4000, // Timeout period
  // Authentication information
  //clientId: 'emqx',
  username: 'web_client',
  password: 'tesis_2021',
  keepalive:60,
  clean: true
}

const webSocket_URL = 'wss://tesismsm2021.ga:8094/mqtt';

const client = mqtt.connect(webSocket_URL, options)

client.on('connect', () => {
    //se subscribe a un topico
    client.subscribe('values', {qos: 0}, (error) => {
        if(!error) {
          //console.log('subscribe succes');
        } else {
          console.log('subscribe fail');
        }
    })

    client.subscribe('valuesStates', {qos: 0}, (error) => {
        if(!error) {
            // console.log('subscribe succes');
        } else {
          console.log('subscribe fail');
        }
    })
})

//devolver mensaje del broker
client.on('message', (topic, message) => {
  console.log('mensaje recibido bajo topico', topic, '->', message.toString());
  process_msg(topic, message);
})

client.on('reconnect', (error) => {
    console.log('error al reconectar', error);
})

client.on('error', (error) => {
    console.log('error de conexion', error);
})

/*
******************************
****** PROCESOS  *************
******************************
*/

function update_values(temp, hum, soilmoisturepercent){
    $("#display_temp").html(temp);
    $("#display_hum").html(hum);
    $("#display_hum_soil").html(soilmoisturepercent);

    //alertas automaticas

    if (temp >= 27) {
          $('#temp-alert').css("display", "block");
      } else
          $('#temp-alert').css("display", "none");

    if (hum >= 60) {
          $('#hum-alert').css("display", "block");
      } else
          $('#hum-alert').css("display", "none");

    if (soilmoisturepercent <= 50) {
          $('#soil-alert ').css("display", "block");
      } else
          $('#soil-alert ').css("display", "none");

    //alertas manuales

    if(temp <=27) {
        $('#temp-alert-manual').css("display", "none");
        $('#box-ventana').css("display", "block");
    }

    if(hum <=45) {
        $('#hum-alert-manual').css("display", "none");
        $('#box-cooler').css("display", "block");
    }

    if(soilmoisturepercent <= 40) {
        $('#soil-alert-manual').css("display", "none");
        $('#box-riego').css("display", "block");
    }
}

function process_msg(topic, message){
  if (topic == "values"){
    var msg = message.toString();
    var sp = msg.split(",");
    var temp = sp[0];
    var hum = sp[1];
    var soilmoisturepercent = sp[2];
    update_values(temp,hum,soilmoisturepercent);
  }
}

function process_msg_states(topic, message){
  if (topic == "valuesStates"){
    var msg = message.toString();
    var sp = msg.split(",");
    var state_window = sp[0];
    var state_cooler = sp[1];
    var state_pump = sp[2];
    console.log(state_window, state_cooler, state_pump);
  }
}

function switch_bomba(){
  if ($('#input_bomba').is(":checked")){
    console.log("La bomba de agua esta prendida");
    client.publish('dispositivoBomba', 'bomba encendida', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }else{
    console.log("La bomba de agua esta apagada");
    client.publish('dispositivoBomba', 'bomba apagada', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }
}

function switch_cooler(){
  if ($('#input_cooler').is(":checked")){
    console.log("La ventilacion esta encendida");
    client.publish('dispositivoCooler', 'ventilacion encendida', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }else{
    console.log("La bomba de agua esta apagada");
    client.publish('dispositivoCooler', 'ventilacion apagada', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }
}

function switch_ventana(){
  if ($('#input_servo').is(":checked")){
    console.log("La ventana esta abierta");
    client.publish('dispositivoServo', 'ventana abierta', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }else{
    console.log("La ventana esta cerrada");
    client.publish('dispositivoServo', 'ventana cerrada', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }
}
