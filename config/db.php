<?php

class Database
{

    public static function dbSelect($username)
    {

        // Configuración de las bases de datos
        $dbConfig = [
            'admin' => ['host' => 'localhost', 'dbname' => 'proyecto', 'user' => 'root', 'pass' => ''],
            'admin2' => ['host' => 'localhost', 'dbname' => 'proyecto2', 'user' => 'root', 'pass' => ''],
            // Agrega más clientes según sea necesario
        ];

        if (!isset($dbConfig[$username])) {
            throw new Exception("Cliente no encontrado.");
        }

        $client = $dbConfig[$username];

        try {
            $db = new mysqli($client['host'], $client['user'], $client['pass'], $client['dbname']);
            $db->query("SET NAMES 'utf8'");
            $db->query("SET time_zone = '-4:00'");

            if ($db->connect_errno) {
                printf("Error de conexión: %s\n", $db->connect_error);
                exit();
            }

            $_SESSION['dbname'] = $client['dbname']; // Crear sesion de la db del usuario

            return $db;


        } catch (Exception $e) {
            echo "Error de conexión: " . $e->getMessage();
        }


        $db->close();
    }
    public static function connect()
    {

        // Obtener base de datos a utilizar 

        $SERVER_NAME = "localhost";
        $USER_NAME = ($_SERVER['SERVER_NAME'] != "localhost") ? "Wilmin" : "root";
        $PASSWORD = ($_SERVER['SERVER_NAME'] != "localhost") ? "Mett@1106" : "";
        $DATABASE_NAME = $_SESSION['dbname'];

        $db = new mysqli($SERVER_NAME, $USER_NAME, $PASSWORD, $DATABASE_NAME);
        $db->query("SET NAMES 'utf8'");
        $db->query("SET time_zone = '-4:00'");

        if ($db->connect_errno) {
            printf("Connect failed: %s\n", $db->connect_error);
            exit();
        }

        return $db;

    }

}
