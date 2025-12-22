<?php
// Настройки подключения к базе данных
$host = 'localhost';     // в XAMPP всегда localhost
$dbname = 'studlib';     // имя твоей базы
$user = 'root';          // пользователь по умолчанию в XAMPP
$password = '';          // пароль по умолчанию пустой

try {
    // Подключаемся через PDO (самый удобный и безопасный способ)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);

    // Настраиваем PDO: показывать ошибки явно
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Устанавливаем кодировку
    $pdo->exec("SET NAMES utf8mb4");

} catch (PDOException $e) {
    // Если подключение не удалось — покажем понятную ошибку
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>