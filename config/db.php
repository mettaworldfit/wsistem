<?php

class Database
{

    private static $mainHost;
    private static $mainUser;
    private static $mainPass;
    private static $mainDB = 'central_config'; // esto no cambia

    // Inicializa credenciales según entorno
    private static function init()
    {
        // Detecta si está en localhost
        $isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']);

        if ($isLocal) {
            // ENTORNO LOCAL
            self::$mainHost = 'localhost';
            self::$mainUser = 'root';
            self::$mainPass = '';
        } else {
            // ENTORNO DE PRODUCCIÓN
            self::$mainHost = 'localhost';
            self::$mainUser = 'Mett@1106';
            self::$mainPass = 'Wilmin';
        }
    }

    public static function dbSelect($username)
    {
        // Inicializa credenciales según el entorno
        self::init();

        // Conectar al sistema de configuración
        $config = new mysqli(self::$mainHost, self::$mainUser, self::$mainPass, self::$mainDB);

        if ($config->connect_errno) {
            throw new Exception("Error conectando a central_config: " . $config->connect_error);
        }

        // Buscar datos del cliente
        $stmt = $config->prepare("SELECT db_host, db_nombre, db_user, db_pass, empresa FROM clientes WHERE usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($host, $dbname, $user, $pass, $empresa);

        if ($stmt->fetch()) {

            $stmt->close();
            $config->close();

            // Conectar a la base de datos del cliente
            $db = new mysqli($host, $user, $pass, $dbname);

            if ($db->connect_errno) {
                throw new Exception("Error al conectar con la base de datos del cliente: " . $db->connect_error);
            }

            // Guardar info en sesión
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


    // Función estática para obtener todas las configuraciones
    public static function getConfig()
    {
        $db = Database::connect();

        // Consulta para obtener todas las configuraciones
        $consulta = "SELECT config_key, config_value FROM configuraciones";
        $resultado = $db->query($consulta);

        // Verificar si la consulta tuvo resultados
        if ($resultado) {
            // Inicializar un array para almacenar las configuraciones
            $configuraciones = [];

            // Recorrer los resultados y guardar las configuraciones en el array
            while ($fila = $resultado->fetch_assoc()) {
                $configuraciones[$fila['config_key']] = $fila['config_value'];
            }

            return $configuraciones;
        } else {
            echo "Error en la consulta: " . $db->error;
            return null; // Retornar null si ocurre un error
        }
    }
}
