<?php
// public/security.php

// XSS Protection: HTML encode output
function escapeHtml($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// SQL Injection Protection: Use PDO with prepared statements
function getDbConnection() {
    $config = require(__DIR__ . '/../config/config.php');
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset=utf8";
    try {
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        exit('Database connection failed: ' . $e->getMessage());
    }
}

// Example query using prepared statements
function getUserData($username) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}