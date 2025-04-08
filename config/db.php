<?php

class Database
{

    public static function dbSelect($username)
    {

        // Configuración de las bases de datos
        $dbConfig = [
            'local' => [
                'host' => 'localhost',
                'dbname' => 'proyecto',
                'user' => 'root',
                'pass' => '',
                'company' => 'Localhost', // Localhost
            ],

            'invitado' => [
                'host' => 'localhost',
                'dbname' => 'invitados',
                'user' => 'Wilmin',
                'pass' => 'Mett@1106',
                'company' => 'Modo Prueba', // Cliente Prueba 1
            ],

            'mambo' => [
                'host' => 'localhost',
                'dbname' => 'mamborestaurant',
                'user' => 'Wilmin',
                'pass' => 'Mett@1106',
                'company' => 'Mamborestaurant', // Cliente Mambocafeteria
            ],

            'master' => [
                'host' => 'localhost',
                'dbname' => 'master_movil',
                'user' => 'Wilmin',
                'pass' => 'Mett@1106',
                'company' => 'Master Movil', // EliSaul Brito
            ],

            'admin' => [
                'host' => 'localhost',
                'dbname' => 'chino_com_mao',
                'user' => 'Wilmin',
                'pass' => 'Mett@1106',
                'company' => 'Chino comunicaciones', // Chino comunicaciones
            ],

            'user' => [
                'host' => 'localhost',
                'dbname' => 'el_furgonazo',
                'user' => 'Wilmin',
                'pass' => 'Mett@1106',
                'company' => 'El furgonazo', // El furgonazo
            ],

            // Agrega más clientes según sea necesario
        ];

        if (!isset($dbConfig[$username])) {
            throw new Exception("Cliente no encontrado.");
        }

        $client = $dbConfig[$username];

        try {
            $db = new mysqli($client['host'], $client['user'], $client['pass'], $client['dbname']);

            if ($db->connect_errno) {
                printf("Error de conexión: %s\n", $db->connect_error);
                exit();
            }

            // Crear sesion de los datos del negocio
            $_SESSION['infoClient'] = [
                "dbname" => $client['dbname'],
                "company" => $client['company'],
            ];

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
        $DATABASE_NAME = $_SESSION['infoClient']['dbname'];

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
