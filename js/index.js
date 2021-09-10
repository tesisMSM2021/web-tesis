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
            console.log('subscribe succes');
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
//funcion time countdown para activar en el front
////////////////////////
///////////////
function countdown( elementName, minutes, seconds ) {
    var element, endTime, hours, mins, msLeft, time;

    function twoDigits( n ) {
        return (n <= 9 ? "0" + n : n);
    }

    function updateTimer() {
        msLeft = endTime - (+new Date);
        if ( msLeft < 1000 ) {
            element.innerHTML = "Time is up!";
        } else {
            time = new Date( msLeft );
            hours = time.getUTCHours();
            mins = time.getUTCMinutes();
            element.innerHTML = (hours ? hours + ':' + twoDigits( mins ) : mins) + ':' + twoDigits( time.getUTCSeconds() );
            setTimeout( updateTimer, time.getUTCMilliseconds() + 500 );
        }
    }

    element = document.getElementById( elementName );
    endTime = (+new Date) + 1000 * (60*minutes + seconds) + 500;
    updateTimer();
}

function update_values(temp, hum, soilmoisturepercent){
    $("#display_temp").html(temp);
    $("#display_hum").html(hum);
    $("#display_hum_soil").html(soilmoisturepercent);

    //alertas automaticas temperatura

    var tempOptima = (temp >= 17 && temp <= 29);
    var tempAceptable = (temp >= 9 && temp <= 16);
    var tempInaceptable = (temp <= -1 && temp <= 8) || temp > 39;
    var humOptima = hum >= 40 && hum <= 60;
    var humAceptable = hum >= 59 && hum <= 80;
    var humInaceptable = hum >= 81 && hum <= 100 || hum >= 0 && hum <= 39;
    var humSOptima = soilmoisturepercent >= 60 && soilmoisturepercent <= 80;
    var humSAceptable = soilmoisturepercent >= 59 && soilmoisturepercent <= 50;
    var humSInaceptable = soilmoisturepercent < 49 || soilmoisturepercent >= 81 && soilmoisturepercent <= 100;

    if (tempOptima) {
        $('#tempOP').css("display", "block");
    } else {
        $('#tempOP').css("display", "none");
    }

    if (tempAceptable) {
        $('#tempAC').css("display", "block");
    } else {
        $('#tempAC').css("display", "none");
    }

    if (tempInaceptable) {
        $('#tempIN').css("display", "block");
        $('#alertVentana').css("display", "block");
        $('#box-ventana').css("display", "none");
    } else {
        $('#tempIN').css("display", "none");
        $('#alertVentana').css("display", "none");
        $('#box-ventana').css("display", "block");
    }

    if (humOptima) {
        $('#humOP').css("display", "block");
    } else {
        $('#humOP').css("display", "none");
    }

    if (humAceptable) {
        $('#humAC').css("display", "block");
    } else {
        $('#humAC').css("display", "none");
    }

    if (humInaceptable) {
        $('#humIN').css("display", "block");
        $('#alertVentilacion').css("display", "block");
        $('#box-cooler').css("display", "none");
    } else {
        $('#humIN').css("display", "none");
        $('#alertVentilacion').css("display", "none");
        $('#box-cooler').css("display", "block");
    }

    if (humSOptima) {
        $('#humSOP').css("display", "block");
    } else {
        $('#humSOP').css("display", "none");
    }

    if (humSAceptable) {
        $('#humSAC').css("display", "block");
    } else {
        $('#humSAC').css("display", "none");
    }

    if (humSInaceptable) {
        $('#humSIN').css("display", "block");
        $('#alertRiego').css("display", "block");
        $('#box-riego').css("display", "none");

    } else {
        $('#humSIN').css("display", "none");
        $('#alertRiego').css("display", "none");
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

//se agrega la variable de estado manual en el front q viene del mensaje////////
////////////////////////////////////////////////////////////
function process_msg_states(topic, message){
  if (topic == "valuesStates"){
    var msg = message.toString();
    var sp = msg.split(",");
    var state_window = sp[0];
    var state_cooler = sp[1];
    var state_pump = sp[2];
    var state_manual = sp[3];
    console.log(state_window, state_cooler, state_pump, state_manual);
  }
}

function switch_ventana(){
  if ($('#input_servo').is(":checked")){
    console.log("La ventana esta abierta");
    client.publish('dispositivoServo', 'ventana abierta', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
    //muestra el alert q dice que la ventana fue abierta manualmente
    ///cuando se clickea el switch switch
    $('#alertVentanaManual').css("display", "block");
    //llama a la funcion countdown de 10 min
    countdown( "ten-countdown", 10, 0 );
  }else{
    console.log("La ventana esta cerrada");
    client.publish('dispositivoServo', 'ventana cerrada', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
    $('#alertVentanaManual').css("display", "none");
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
