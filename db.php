<?php
// Ajusta estos valores a tu servidor
$host = 'localhost';
$db   = 'tetroleo';
$user = 'root';
$pass = '';

// Hacemos la conexión con PDO (recomendado)
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}
