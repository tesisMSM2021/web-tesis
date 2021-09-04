<?php
  session_start();
  $_SESSION['logged'] = false;

  $msg="";
  $email="";

  if(isset($_POST['email']) && isset($_POST['password'])) {

    if ($_POST['email']==""){
      $msg.="Debe ingresar un email <br>";
    }else if ($_POST['password']=="") {
      $msg.="Debe ingresar la clave <br>";
    }else {
      $email = strip_tags($_POST['email']);
      $password= sha1(strip_tags($_POST['password']));

      //momento de conectarnos a db servidor/usuario/passwor/nombreBD
      $conn = mysqli_connect("localhost","admin_tesis2021","tesis_2021","admin_tesismsm");

      if ($conn==false){
        echo "Hubo un problema al conectarse a María DB";
        die();
      }

      $result = $conn->query("SELECT * FROM `users` WHERE `users_email` = '".$email."' AND  `users_password` = '".$password."' ");
      $users = $result->fetch_all(MYSQLI_ASSOC);

      //cuento cuantos elementos tiene $tabla,
      $count = count($users);

      if ($count == 1){

        //cargo datos del usuario en variables de sesión
        $_SESSION['users_id'] = $users[0]['users_id'];
        $_SESSION['users_email'] = $users[0]['users_email'];

        $msg .= "Usuario logueado con exito!";
        $_SESSION['logged'] = true;

        echo '<meta http-equiv="refresh" content="2; url=dashboard.php">';
      }else{
        $msg .= "Acceso denegado!!!";
        $_SESSION['logged'] = false;
      }
    }
  }

  //registro

  //momento de conectarnos a db
  $conn = mysqli_connect("localhost","admin_tesis2021","tesis_2021","admin_tesismsm");

  if ($conn==false){
    echo "Hubo un problema al conectarse a María DB";
    die();
  }

  //declaramos variables vacias servirán también para repoblar el formulario
  $email = "";
  $password = "";
  $password_r = "";
  $msg = "";

  if( isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_r'])) {

    $email = strip_tags($_POST['email']);
    $password = strip_tags($_POST['password']);
    $password_r = strip_tags($_POST['password_r']);


    if ($password==$password_r){

      //aquí como todo estuvo OK, resta controlar que no exista previamente el mail ingresado en la tabla users.
      $result = $conn->query("SELECT * FROM `users` WHERE `users_email` = '".$email."' ");
      $users = $result->fetch_all(MYSQLI_ASSOC);

      //cuento cuantos elementos tiene $tabla,
      $count = count($users);

      //solo si no hay un usuario con mismo mail, procedemos a insertar fila con nuevo usuario
      if ($count == 0){
        $password = sha1($password); //encriptar clave con sha1
        $conn->query("INSERT INTO `users` (`users_email`, `users_password`) VALUES ('".$email."', '".$password."');");
        $msg.="Usuario creado correctamente, ingrese haciendo  <a href='login.php'>clic aquí</a> <br>";
      }else{
        $msg.="El mail ingresado ya existe <br>";
      }

    }else{
      $msg = "Las claves no coinciden";
    }

  }else{
    $msg = "Complete el formulario";
  }
?>
