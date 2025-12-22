<?php
require 'db.php';
session_start();
$user_id = 1;  // Для теста. Потом $_SESSION['user_id']

// Получаем id папки
$folder_id = $_GET['id'] ?? null;
if (!$folder_id || !is_numeric($folder_id)) {
    die("Ошибка: не указана папка");
}

// Обработка сохранения и перемещения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $new_name = trim($_POST['folder_name'] ?? '');
        $new_tags = trim($_POST['folder_tags'] ?? '');
        if ($new_name !== '') {
            $stmt = $pdo->prepare("UPDATE folders SET name = ?, tags = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$new_name, $new_tags, $folder_id, $user_id]);
        }
        header("Location: folder_view.php?id=$folder_id");
        exit;
    }

    if (isset($_POST['move']) && !empty($_POST['selected_materials']) && !empty($_POST['target_folder'])) {
        $target = $_POST['target_folder'];
        $selected = $_POST['selected_materials'];
        $placeholders = str_repeat('?,', count($selected) - 1) . '?';
        $stmt = $pdo->prepare("UPDATE materials SET folder_id = ? WHERE id IN ($placeholders) AND user_id = ?");
        $params = array_merge([$target], $selected, [$user_id]);
        $stmt->execute($params);
        header("Location: folder_edit.php?id=$folder_id");  // остаёмся в редактировании
        exit;
    }
}

// Получаем данные папки
$stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ? AND user_id = ?");
$stmt->execute([$folder_id, $user_id]);
$folder = $stmt->fetch();

if (!$folder) {
    die("Папка не найдена или доступ запрещён");
}

// Получаем материалы в папке
$stmt = $pdo->prepare("
    SELECT m.*, mt.name AS type_name
    FROM materials m
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.folder_id = ? AND m.user_id = ?
");
$stmt->execute([$folder_id, $user_id]);
$materials = $stmt->fetchAll();

// Получаем другие папки для перемещения
$stmt = $pdo->prepare("SELECT id, name FROM folders WHERE user_id = ? AND id != ? ORDER BY name");
$stmt->execute([$user_id, $folder_id]);
$other_folders = $stmt->fetchAll();
?>
