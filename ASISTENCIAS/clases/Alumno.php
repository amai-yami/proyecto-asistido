<?php

class Alumno {
    private $conn;
    private $table = 'alumnos';

    public $id;
    public $nombre;
    public $apellido;
    public $telefono;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear un nuevo alumno
    public function create() {
        $query = "INSERT INTO " . $this->table . " (nombre, apellido, telefono) VALUES (:nombre, :apellido, :telefono)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':telefono', $this->telefono);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Otros métodos (read, update, delete) se podrían implementar aquí
}

