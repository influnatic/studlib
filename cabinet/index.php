<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$display_name = $_SESSION['display_name'] ?? 'User';

// Получаем данные пользователя из БД для отображения текущего аватара
require_once __DIR__ . '/../config/user_avatar.php';
$current_avatar = getUserAvatar($pdo, $user_id);

// Получаем список доступных аватаров
$avatarsDir = '../assets/avatars/';
$availableAvatars = [];
if (is_dir($avatarsDir)) {
    $files = scandir($avatarsDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
            $availableAvatars[] = $file;
        }
    }
}

// Обработка сохранения аватара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_avatar'])) {
    $new_avatar = trim($_POST['avatar'] ?? '');
    if ($new_avatar !== '') {
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$new_avatar, $user_id]);
        $_SESSION['avatar'] = $new_avatar;
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLib — Личный кабинет</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>


    <!-- Личный кабинет -->
    <div class="cabinet-content">
        <link rel="stylesheet" href="../assets/css/cabinet.css">
        
        <h1 class="cabinet-name"><?= htmlspecialchars($display_name) ?></h1>
        
        <!-- Выбор аватара -->
        <div class="avatar-selector-section">
            <form method="POST" class="avatar-form">
                <div class="avatar-grid">
                    <?php foreach ($availableAvatars as $avatarFile): ?>
                        <label class="avatar-option">
                            <input type="radio" name="avatar" value="<?= htmlspecialchars($avatarFile) ?>" 
                                   <?= ($current_avatar === $avatarFile) ? 'checked' : '' ?>>
                            <div class="avatar-preview">
                                <img src="../assets/avatars/<?= htmlspecialchars($avatarFile) ?>" 
                                     alt="<?= htmlspecialchars(pathinfo($avatarFile, PATHINFO_FILENAME)) ?>">
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="avatar-form-actions">
                    <button type="submit" name="save_avatar" class="btn btn-save-avatar">Сохранить аватар</button>
                    <a href="../auth/logout.php" class="btn btn-logout">Выйти</a>
                </div>
            </form>
        </div>
    </div>


</body>
</html>