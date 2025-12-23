<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user = 'root';
$password = 'root';
$db = 'php_online_store';
$host = 'localhost';
$port = 3306;

$dsn = 'mysql:host='.$host.';dbname='.$db.';port='.$port;
$pdo = new PDO($dsn, $user, $password);
$pdo->exec('set names utf8');