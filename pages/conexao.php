<?php

session_start();

$host = 'localhost';
$usuario = 'root';
$senha = 'serra';
$banco = 'gestormax';
$port = '3306';

$conn = new mysqli($host, $usuario, $senha, $banco);


if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
