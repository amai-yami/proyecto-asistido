<?php

class Profesor
{
    public $id;  // Agregar la propiedad id
    public $nombre;
    public $apellido;
    public $correo_electronico;
    public $telefono;
    public $departamento;

    // Constructor modificado para incluir el id como parámetro opcional
    public function __construct($nombre, $apellido, $correo_electronico, $telefono, $departamento, $id = null) {
        $this->id = $id;  // Asignar el id
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->correo_electronico = $correo_electronico;
        $this->telefono = $telefono;
        $this->departamento = $departamento;
    }
}
/*
    public function saludar(){
    
        return"hola soy $this->nombre<br>";

    }
    public function especialidad(){
    
        return"mi especialidad es  $this->materia/<br>";

    }

    public function cambiar_materia($materia){

        $this->materia = $materia;
        return "la nueva materia es $this->materia<br><br>";

    }
    */


