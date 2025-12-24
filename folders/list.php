<?php
require '../config/db.php';  // подключение к базе
require '../config/user_avatar.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;  // для теста, потом $_SESSION['user_id']

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $user_id);

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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>
<main>
<?php foreach ($folders as $folder):
        $folder_mats = $materials_by_folder[$folder['id']] ?? [];
    ?>

        <div class="folder-section">
            <div class="folder-header">
                <div class="folder-icon">
                    <?php if (!empty($folder['icon'])): ?>
                        <img src="../assets/icons/<?php echo htmlspecialchars($folder['icon']); ?>" alt="<?php echo htmlspecialchars($folder['name']); ?>">
                    <?php endif; ?>
                </div>
                <div class="folder-title"><?php echo htmlspecialchars($folder['name']); ?></div>
                <a href="../folders/view.php?id=<?php echo $folder['id']; ?>" class="open-folder-btn">Открыть папку</a>
            </div>

        <?php if (!empty($folder_mats)): ?>
            <div class="materials-grid">
                <?php foreach ($folder_mats as $mat): ?>
                    <a href="../materials/view.php?id=<?php echo $mat['id']; ?>&return_to=<?php echo urlencode('folders/list.php'); ?>" class="material-card material-card-yellow">
                        <h3><?php echo htmlspecialchars($mat['name']); ?></h3>
                        <p>Тип: <?php echo htmlspecialchars($mat['type_name'] ?? 'Не указан'); ?></p>
                        <p>Теги: <?php echo htmlspecialchars($mat['tags']); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-folder">
                В папке пока нет материалов
            </div>
        <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (empty($folders)): ?>
        <p class="empty_message">У вас пока нет папок. Добавьте первую!</p>
    <?php endif; ?>
</main>
</body>

</html>

