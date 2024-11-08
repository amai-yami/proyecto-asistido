<?php

class Usuario {
    private $conn;
    private $table = 'Usuarios';

    public $id;
    public $nombre;
    public $apellido;
    public $correo_electronico;
    public $telefono;
    public $usuario; //  el nombre de la persona que desea ingresar al sistema
    public $contrasena;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear un nuevo usuario
    public function create() {
        $query = "INSERT INTO " . $this->table . " (nombre, apellido, correo_electronico, telefono, usuario, contrasena) 
                  VALUES (:nombre, :apellido, :correo_electronico, :telefono, :usuario, :contrasena)";
        
        $stmt = $this->conn->prepare($query);
        
        // Vincular parámetros
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':correo_electronico', $this->correo_electronico);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':usuario', $this->usuario);
        $stmt->bindParam(':contrasena', $this->contrasena);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Otros métodos (read, update, delete) se podrían implementar aquí
}