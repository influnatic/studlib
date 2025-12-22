<?php
require 'db.php';  // подключение к базе
session_start();
$user_id = 1;  // для теста, потом $_SESSION['user_id']

// Получаем все папки пользователя
$stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ? ORDER BY name");
$stmt->execute([$user_id]);
$folders = $stmt->fetchAll();

// Получаем все материалы с типом
$stmt = $pdo->prepare("
    SELECT m.*, mt.name AS type_name
    FROM materials m
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.user_id = ?
");
$stmt->execute([$user_id]);
$all_materials = $stmt->fetchAll();

// Группируем материалы по folder_id
$materials_by_folder = [];
foreach ($all_materials as $mat) {
    $fid = $mat['folder_id'] ?? 0;  // если null — в корне, но у тебя все в папках
    $materials_by_folder[$fid][] = $mat;
}
?>