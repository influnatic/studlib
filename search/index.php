<?php
require '../config/db.php';
require '../config/user_avatar.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;  // потом $_SESSION['user_id']

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $user_id);

$query = $_GET['tags'] ?? '';
$search_tags = [];

if (!empty($query)) {
    $tags_input = str_replace('#', ' ', $query);
    $tags_array = array_filter(array_map('trim', explode(' ', $tags_input)));
    $search_tags = array_map('strtolower', $tags_array);
}

$matching_folders = [];
$matching_materials = [];

if (!empty($search_tags)) {
    $like_conditions = [];
    $params = [$user_id];
    foreach ($search_tags as $i => $tag) {
        $like_conditions[] = "LOWER(tags) LIKE ?";
        $params[] = '%' . $tag . '%';
    }
    $where = implode(' OR ', $like_conditions);

    // Найденные папки
    $stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ? AND ($where) ORDER BY name");
    $stmt->execute($params);
    $matching_folders = $stmt->fetchAll();

    // Найденные материалы
    $stmt = $pdo->prepare("
        SELECT m.*, mt.name AS type_name
        FROM materials m
        LEFT JOIN mat_types mt ON m.type_id = mt.id
        WHERE m.user_id = ? AND ($where)
        ORDER BY m.name
    ");
    $stmt->execute($params);
    $all_matching_materials = $stmt->fetchAll();
    
    // Группируем материалы по folder_id
    $materials_by_folder = [];
    foreach ($all_matching_materials as $mat) {
        $fid = $mat['folder_id'] ?? 0;
        $materials_by_folder[$fid][] = $mat;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>
<main>
    <form method="GET">
        <input type="text" name="tags" class="search_line" placeholder="Введите #теги через пробел" value="<?php echo htmlspecialchars($query); ?>">
    </form>

    <?php if (empty($search_tags)): ?>

    <?php elseif (empty($matching_folders) && empty($all_matching_materials)): ?>
        <div class="no-results">Ничего не найдено по запросу: <?php echo htmlspecialchars($query); ?></div>

    <?php else: ?>
        <!-- Показываем найденные папки с их материалами -->
        <?php foreach ($matching_folders as $folder): 
            $folder_mats = $materials_by_folder[$folder['id']] ?? [];
        ?>
            <div class="search-folder-section">
                <div class="folder-section">
                    <div class="folder-header">
                        <div class="folder-icon">
                            <?php if (!empty($folder['icon'])): ?>
                                <img src="../assets/icons/<?php echo htmlspecialchars($folder['icon']); ?>" alt="<?php echo htmlspecialchars($folder['name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="folder-info-wrapper">
                            <div class="folder-title"><?php echo htmlspecialchars($folder['name']); ?></div>
                            <?php if ($folder['tags']): ?>
                                <p class="tags_folder_inline">Теги: <?php echo htmlspecialchars($folder['tags']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="folder-actions">
                            <a href="../folders/view.php?id=<?php echo $folder['id']; ?>" class="open-folder-btn">Открыть папку</a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($folder_mats)): ?>
                    <div class="materials-grid">
                        <?php foreach ($folder_mats as $mat): ?>
                            <a href="../materials/view.php?id=<?php echo $mat['id']; ?>&return_to=<?php echo urlencode('search/index.php' . ($query ? '?tags=' . urlencode($query) : '')); ?>" class="material-card material-card-yellow">
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
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>

