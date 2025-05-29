<?php
$host = 'localhost';
$dbname = 'odaw';
$username = 'root';
$password = 'Brecho@2023';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

function formatarData($data, $formato = 'd/m/Y') {
    return date($formato, strtotime($data));
}
?>