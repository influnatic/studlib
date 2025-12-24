<?php
require '../config/db.php';  // подключение к базе
require '../config/user_avatar.php';
session_start();
$user_id = 1;  // Для теста. Потом замени на $_SESSION['user_id'] после авторизации

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $user_id);

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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($folder['name']); ?> — StudLib</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>
<main>
    <div class="folder-section">
        <div class="folder-header">
            <div class="folder-icon">
                <?php if (!empty($folder['icon'])): ?>
                    <img src="../assets/icons/<?php echo htmlspecialchars($folder['icon']); ?>" alt="<?php echo htmlspecialchars($folder['name']); ?>">
                <?php endif; ?>
            </div>
            <div class="folder-info-wrapper">
                <div class="folder-title"><?php echo htmlspecialchars($folder['name']); ?></div>
                <?php if (!empty($folder['tags'])): ?>
                    <p class="tags_folder_inline">
                        Теги: <?php echo htmlspecialchars($folder['tags']); ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="folder-actions">
                <a href="../folders/edit.php?id=<?php echo $folder['id']; ?>" class="button_edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="svg-inline-icon">
                        <path d="M11 4H7.2C6.0799 4 5.51984 4 5.09202 4.21799C4.71569 4.40974 4.40973 4.7157 4.21799 5.09202C4 5.51985 4 6.0799 4 7.2V16.8C4 17.9201 4 18.4802 4.21799 18.908C4.40973 19.2843 4.71569 19.5903 5.09202 19.782C5.51984 20 6.0799 20 7.2 20H16.8C17.9201 20 18.4802 20 18.908 19.782C19.2843 19.5903 19.5903 19.2843 19.782 18.908C20 18.4802 20 17.9201 20 16.8V12.5M15.5 5.5L18.3284 8.32843M10.7627 10.2373L17.411 3.58902C18.192 2.80797 19.4584 2.80797 20.2394 3.58902C21.0205 4.37007 21.0205 5.6364 20.2394 6.41745L13.3774 13.2794C12.6158 14.0411 12.235 14.4219 11.8012 14.7247C11.4162 14.9936 11.0009 15.2162 10.564 15.3882C10.0717 15.582 9.54378 15.6885 8.48793 15.9016L8 16L8.04745 15.6678C8.21536 14.4925 8.29932 13.9048 8.49029 13.3561C8.65975 12.8692 8.89125 12.4063 9.17906 11.9786C9.50341 11.4966 9.92319 11.0768 10.7627 10.2373Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Редактировать
                </a>
            </div>
        </div>

        <?php if (!empty($materials)): ?>
            <div class="materials-grid">
                <?php foreach ($materials as $mat): ?>
                    <a href="../materials/view.php?id=<?php echo $mat['id']; ?>&return_to=<?php echo urlencode('folders/view.php?id=' . $folder_id); ?>" class="material-card material-card-yellow">
                        <h3><?php echo htmlspecialchars($mat['name']); ?></h3>
                        <p>Тип: <?php echo htmlspecialchars($mat['type_name'] ?? 'Не указан'); ?></p>
                        <?php if ($mat['tags']): ?>
                            <p>Теги: <?php echo htmlspecialchars($mat['tags']); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-folder">
                В папке пока нет материалов
            </div>
        <?php endif; ?>
    </div>



</main>
</body>
</html>

