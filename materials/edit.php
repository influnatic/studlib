<?php
require '../config/db.php';
require '../config/user_avatar.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;  // Для теста. Потом $_SESSION['user_id']

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $user_id);

// Получаем ID материала из GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Неверный ID материала");
}

// Сохраняем return_to для передачи при редиректе
$return_to_param = isset($_GET['return_to']) ? $_GET['return_to'] : '';

// Получаем список типов материалов
$typeStmt = $pdo->query("SELECT id, name FROM mat_types");
$types = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем текущий материал с данными о папке и типе
$stmt = $pdo->prepare("
    SELECT m.*, 
           f.name AS folder_name,
           f.id AS folder_id,
           f.icon AS folder_icon,
           mt.name AS type_name,
           mt.id AS type_id
    FROM materials m
    LEFT JOIN folders f ON m.folder_id = f.id
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.id = ? AND m.user_id = ?
");
$stmt->execute([$id, $user_id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    die("Материал не найден или доступ запрещён");
}

// Обработка сохранения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $name = trim($_POST['name'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);

        if ($name !== '' && $typeId > 0) {
            // Используем текущие значения папки и тегов из материала
            $folderId = $material['folder_id'] ?? null;
            $tags = $material['tags'] ?? '';

            // Обновляем материал
            $update = $pdo->prepare("
                UPDATE materials 
                SET name = ?, content = ?, tags = ?, folder_id = ?, type_id = ?
                WHERE id = ? AND user_id = ?
            ");
            $update->execute([$name, $content, $tags, $folderId, $typeId, $id, $user_id]);

            $return_to = $return_to_param ? '&return_to=' . urlencode($return_to_param) : '';
            header("Location: ../materials/view.php?id=$id&success=1$return_to");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($material['name']); ?> — Редактирование — StudLib</title>
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
            </div>
        </div>
    <?php endif; ?>

    <form method="POST">
        <section>
            <p class="material-edit-row">
                <strong>Название:</strong> 
                <input type="text" name="name" value="<?php echo htmlspecialchars($material['name']); ?>" class="material-edit-inline" required>
            </p>
            <p class="material-edit-row">
                <strong>Формат:</strong> 
                <select name="type_id" class="material-edit-inline" required>
                    <option value="">-- Выберите тип --</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>" <?php echo ($material['type_id'] == $type['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
        </section>

        <section>
            <h3 class="instruct">Материал</h3>
            <textarea name="content" class="material-content-edit" placeholder="Содержание материала"><?php echo htmlspecialchars($material['content'] ?? ''); ?></textarea>
        </section>

        <div class="action-bar-compact">
            <!-- Сохранение -->
            <button type="submit" name="save" class="action-btn" title="Сохранить изменения">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="svg-no-fill">
                    <path d="M20 6L9 17L4 12" stroke="#2A2A2A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
                <span class="tooltip">Сохранить изменения</span>
            </button>

            <!-- Отмена -->
            <a href="../materials/view.php?id=<?php echo $id; ?><?php echo $return_to_param ? '&return_to=' . urlencode($return_to_param) : ''; ?>" class="action-btn danger" title="Отмена">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Отмена</span>
            </a>
        </div>
    </form>
</main>

</body>
</html>
