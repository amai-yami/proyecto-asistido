<?php
        class Instituciones
        {
            public $nombre;
            public $direccion;
            public $telefono;
            public $correo_electronico;
        
           public function __construct( $nombre,$direccion,$telefono,$correo_electronico) {
                $this->nombre = $nombre;
                $this->direccion= $direccion;
                $this->telefono = $telefono;
                $this->correo_electronico = $correo_electronico;
            }
         }
        