<?php
$host = "localhost";
$db   = "tcc"; 
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('America/Sao_Paulo');
?>