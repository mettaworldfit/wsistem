<?php

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$server_name = ($_SERVER['SERVER_NAME'] != "localhost") ? $protocol.$_SERVER['SERVER_NAME']."/" : $protocol.$_SERVER['SERVER_NAME']."/proyecto/";

define("base_url", $server_name);
define("DEFAULT_CONTROLLER", "homeController");
define("DEFAULT_ACTION", "index");

define("NO_LOGIN_CONTROLLER", "usersController");
define("NO_LOGIN_ACTION", "login");

define("NO_FOUND", "error");





