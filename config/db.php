<?php

class Database
{

    private static $mainHost = 'localhost';
    private static $mainUser = 'Wilmin';
    private static $mainPass = 'Mett@1106';
    private static $mainDB   = 'central_config';

    public static function dbSelect($username)
    {
       // Conectar al sistema de configuración
        $config = new mysqli(self::$mainHost, self::$mainUser, self::$mainPass, self::$mainDB);

        if ($config->connect_errno) {
            throw new Exception("Error conectando a central_config: " . $config->connect_error);
        }

        // Buscar el cliente
        $stmt = $config->prepare("SELECT db_host, db_nombre, db_user, db_pass, empresa FROM clientes WHERE usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($host, $dbname, $user, $pass, $empresa);

        if ($stmt->fetch()) {
            $stmt->close();
            $config->close();

            $db = new mysqli($host, $user, $pass, $dbname);
            if ($db->connect_errno) {
                throw new Exception("Error al conectar con la base de datos del cliente: " . $db->connect_error);
            }

            $_SESSION['infoClient'] = [
                'dbname' => $dbname,
                'company' => $empresa,
            ];

            return $db;
        } else {
            throw new Exception("Cliente '$username' no encontrado.");
        }
    }


    public static function connect()
    {
        if (!isset($_SESSION['infoClient'])) {
            throw new Exception("No hay cliente seleccionado.");
        }

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
