<?php
require_once '../config/db.php'; // Подключение к БД

// Получаем ID материала из POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    die("Неверный ID материала.");
}

// Удаляем материал из базы
$stmt = $pdo->prepare("DELETE FROM materials WHERE id = ?");
$stmt->execute([$id]);

// Перенаправляем на страницу списка материалов
header("Location: ../folders/list.php?deleted=1");
exit;
?>
