<?php 
//Conexão DB
$host =  '127.0.0.1';
$db = 'pdv_system';
$user = 'root';
$password = '';

try{
    $pdo = new PDO("mysql:host=$host; dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    die("Erro ao conectar ao banco: ". $e->getMessage());
}