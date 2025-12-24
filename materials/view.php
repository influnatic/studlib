<?php
require '../config/db.php';
require '../config/user_avatar.php';
session_start();

// Получаем ID материала из GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Неверный ID материала");

// Получаем текущий материал с данными о папке и типе
$stmt = $pdo->prepare("
    SELECT m.*, 
           f.name AS folder_name,
           f.id AS folder_id,
           f.icon AS folder_icon,
           mt.name AS type_name
    FROM materials m
    LEFT JOIN folders f ON m.folder_id = f.id
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.id = ?
");
$stmt->execute([$id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$material) die("Материал не найден");

// Проверяем параметр success для отображения сообщения
$show_success = isset($_GET['success']) && $_GET['success'] == '1';

// Определяем, откуда пришел пользователь для кнопки "Назад"
// Используем параметр return_to из URL, если он передан
$return_url = $_GET['return_to'] ?? null;
if ($return_url) {
    // Если передан относительный путь, добавляем ../
    $return_url = '../' . $return_url;
} else {
    // Если параметр не передан, возвращаемся к папке материала или списку папок
    $return_url = $material['folder_id'] 
        ? "../folders/view.php?id=" . $material['folder_id']
        : "../folders/list.php";
}

// Предположим, что у пользователя есть ID (например, user_id = 1)
$userId = $_SESSION['user_id'] ?? $material['user_id'] ?? 1;

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $userId);

// Получаем ID предыдущего материала (меньше текущего ID)
$prevStmt = $pdo->prepare("
    SELECT id FROM materials 
    WHERE user_id = ? AND id < ? 
    ORDER BY id DESC LIMIT 1
");
$prevStmt->execute([$userId, $id]);
$previous = $prevStmt->fetch(PDO::FETCH_ASSOC);
$previousId = $previous['id'] ?? null;

// Получаем ID следующего материала (больше текущего ID)
$nextStmt = $pdo->prepare("
    SELECT id FROM materials 
    WHERE user_id = ? AND id > ? 
    ORDER BY id ASC LIMIT 1
");
$nextStmt->execute([$userId, $id]);
$next = $nextStmt->fetch(PDO::FETCH_ASSOC);
$nextId = $next['id'] ?? null;
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLib — Просмотр материала</title>
    <link rel="stylesheet" href="../assets/css/materials.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php if ($material['folder_id']): ?>
        <div class="folder-section">
            <div class="folder-header">
                <div class="folder-icon">
                    <?php if (!empty($material['folder_icon'])): ?>
                        <img src="../assets/icons/<?php echo htmlspecialchars($material['folder_icon']); ?>" alt="<?php echo htmlspecialchars($material['folder_name']); ?>">
                    <?php endif; ?>
                </div>
                <div class="folder-info-wrapper">
                    <div class="folder-title"><?php echo htmlspecialchars($material['folder_name']); ?></div>
                </div>
                <div class="folder-actions">
                    <a href="<?php echo htmlspecialchars($return_url); ?>" class="button_edit">
                        ← Назад
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($show_success): ?>
        <p class="success-message">
            ✔ Изменения сохранены
        </p>
    <?php endif; ?>

    <section>
        <p class="material-view-row"><strong>Название:</strong> <?= htmlspecialchars($material['name'] ?? '') ?></p>
        <p class="material-view-row"><strong>Формат:</strong> <?= htmlspecialchars($material['type_name'] ?? 'Не указан') ?></p>
    </section>

    <section>
        <h3 class="instruct">Материал</h3>

        <div class="material-text collapsed" id="materialText">
            <?= nl2br(htmlspecialchars($material['content'] ?? '')) ?>
        </div>

        <?php if (!empty($material['path'])): ?>
        <p class="attached-file-link">
            <a href="<?= htmlspecialchars($material['path']) ?>" target="_blank">📎 Открыть прикреплённый файл</a>
        </p>
        <?php endif; ?>
    </section>

    <div class="action-bar-compact">
        <!-- Предыдущий материал -->
        <form action="../materials/view.php" method="get">
            <input type="hidden" name="id" value="<?= $previousId ?? $material['id'] ?>">
            <?php if ($return_url): ?>
                <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_url); ?>">
            <?php endif; ?>
            <button class="action-btn prev" type="submit">
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2893 5.70708C13.8988 5.31655 13.2657 5.31655 12.8751 5.70708L7.98768 10.5993C7.20729 11.3805 7.2076 12.6463 7.98837 13.427L12.8787 18.3174C13.2693 18.7079 13.9024 18.7079 14.293 18.3174C14.6835 17.9269 14.6835 17.2937 14.293 16.9032L10.1073 12.7175C9.71678 12.327 9.71678 11.6939 10.1073 11.3033L14.2893 7.12129C14.6799 6.73077 14.6799 6.0976 14.2893 5.70708Z" fill="#0F0F0F"/>
                </svg>
                <span class="tooltip">Предыдущий материал</span>
            </button>
        </form>

        <!-- Поделиться -->
        <button class="action-btn share" onclick="navigator.clipboard.writeText(window.location.href)">
            <img src="../assets/img/link.svg" alt="Поделиться" class="action-icon">
            <span class="tooltip">Скопировать ссылку</span>
        </button>

        <!-- Редактирование -->
        <form action="../materials/edit.php" method="get">
            <input type="hidden" name="id" value="<?= $material['id'] ?>">
            <?php if ($return_url): ?>
                <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_url); ?>">
            <?php endif; ?>
            <button class="action-btn rotate" type="submit">
                <img src="../assets/img/edit.svg" alt="Редактировать" class="action-icon">
                <span class="tooltip">Редактировать материал</span>
            </button>
        </form>

        <!-- Удаление -->
        <form action="../materials/delete.php" method="post">
            <input type="hidden" name="id" value="<?= $material['id'] ?>">
            <button class="action-btn danger" type="submit">
                <img src="../assets/img/delete.svg" alt="Удалить" class="action-icon">
                <span class="tooltip">Удалить материал</span>
            </button>
        </form>

        <!-- Следующий материал -->
        <form action="../materials/view.php" method="get">
            <input type="hidden" name="id" value="<?= $nextId ?? $material['id'] ?>">
            <?php if ($return_url): ?>
                <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_url); ?>">
            <?php endif; ?>
            <button class="action-btn next" type="submit">
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="#0F0F0F"/>
                </svg>
                <span class="tooltip">Следующий материал</span>
            </button>
        </form>
    </div>

</main>

<script>
    // Разворачивание материала при клике на область
    document.addEventListener('DOMContentLoaded', function() {
        const materialText = document.getElementById('materialText');
        
        if (materialText) {
            materialText.addEventListener('click', () => {
                materialText.classList.toggle('expanded');
                materialText.classList.toggle('collapsed');
            });
        }
    });
</script>

</body>
</html>
