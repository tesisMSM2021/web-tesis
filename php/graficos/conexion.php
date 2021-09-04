<?php
	class conexion{
		private $servidor;
		private $usuario;
		private $contrasena;
		private $basedatos;
		public $conexion;
		public function __construct(){
		  $this->servidor = "localhost";
			$this->usuario = "admin_tesis2021";
			$this->contrasena = "tesis_2021";
			$this->basedatos = "admin_tesismsm";
		}
		function conectar(){
			$this->conexion = new mysqli($this->servidor,$this->usuario,$this->contrasena,$this->basedatos);
			$this->conexion->set_charset("utf8");
		}
		function cerrar(){
			$this->conexion->close();
		}
	}
?>
