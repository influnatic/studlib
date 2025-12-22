<?php
require 'db.php';  // подключение к базе
session_start();
$user_id = 1;  // Для теста. Потом замени на $_SESSION['user_id'] после авторизации

// Получаем id папки из URL
$folder_id = $_GET['id'] ?? null;

if (!$folder_id || !is_numeric($folder_id)) {
    die("Ошибка: не указана папка");
}

// Получаем информацию о папке
$stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ? AND user_id = ?");
$stmt->execute([$folder_id, $user_id]);
$folder = $stmt->fetch();

if (!$folder) {
    die("Папка не найдена или доступ запрещён");
}

// Получаем материалы только из этой папки
$stmt = $pdo->prepare("
    SELECT m.*, mt.name AS type_name
    FROM materials m
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.folder_id = ? AND m.user_id = ?
");
$stmt->execute([$folder_id, $user_id]);
$materials = $stmt->fetchAll();
?>