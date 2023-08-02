<?php

define('dbhost', 'localhost');
define('dbuser', 'root');
define('dbpass', '');
define('dbname', 'app_sistema-medico');

$conn = new PDO("mysql:host=".dbhost."; dbname=".dbname, dbuser, dbpass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (!$conn){
    die("Error". mysqli_connect_error());
}

?>


