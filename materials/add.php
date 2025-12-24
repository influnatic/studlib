<?php
require '../config/db.php';
require '../config/user_avatar.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;  // Для теста. Потом $_SESSION['user_id']

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $user_id);

// Получаем список типов материалов
$typeStmt = $pdo->query("SELECT id, name FROM mat_types");
$types = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем список папок пользователя для выбора
$foldersStmt = $pdo->prepare("SELECT id, name FROM folders WHERE user_id = ? ORDER BY name");
$foldersStmt->execute([$user_id]);
$allFolders = $foldersStmt->fetchAll(PDO::FETCH_ASSOC);

// Создаём папку uploads если не существует
if (!is_dir('../uploads')) {
    mkdir('../uploads', 0777, true);
}

$error = '';

// Обработка формы добавления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $name = trim($_POST['name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $filePath = '';
        $postFolderId = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : 0;

        // Проверка файла
        if (!empty($_FILES['file']['name'])) {
            $fileName = basename($_FILES['file']['name']);
            $targetPath = '../uploads/' . time() . '_' . $fileName;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $filePath = $targetPath;
            } else {
                $error = "Ошибка при загрузке файла.";
            }
        }

        if (empty($error)) {
            if ($name === '') {
                $error = "Название материала обязательно.";
            } elseif ($typeId <= 0) {
                $error = "Выберите тип материала.";
            } elseif ($postFolderId <= 0) {
                $error = "Папка не указана.";
            } else {
                // Проверяем, что папка существует и принадлежит пользователю
                $folderCheck = $pdo->prepare("SELECT id FROM folders WHERE id = ? AND user_id = ?");
                $folderCheck->execute([$postFolderId, $user_id]);
                if (!$folderCheck->fetch()) {
                    $error = "Папка не найдена или нет доступа.";
                } else {
                    // Добавляем материал
                    $stmt = $pdo->prepare("
                        INSERT INTO materials (name, type_id, content, folder_id, tags, user_id, path)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$name, $typeId, $content, $postFolderId, '', $user_id, $filePath]);

                    $newId = $pdo->lastInsertId();
                    header("Location: ../materials/view.php?id=$newId&success=1");
                    exit;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание материала — StudLib</title>
    <link rel="stylesheet" href="../assets/css/materials.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php if (!empty($error)): ?>
        <p class="error-message">
            ✗ <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <?php if (empty($allFolders)): ?>
        <p class="error-message">
            ✗ У вас нет папок. Пожалуйста, <a href="../folders/add.php" class="link-underlined">создайте папку</a> перед добавлением материала.
        </p>
    <?php else: ?>
    <form method="POST" enctype="multipart/form-data">
        <section>
            <p class="material-edit-row">
                <strong>Название:</strong> 
                <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" class="material-edit-inline" placeholder="Введите название материала" required>
            </p>
            <p class="material-edit-row">
                <strong>Формат:</strong> 
                <select name="type_id" class="material-edit-inline" required>
                    <option value="">-- Выберите тип --</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>" <?php echo (isset($_POST['type_id']) && $_POST['type_id'] == $type['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p class="material-edit-row">
                <strong>Папка:</strong> 
                <select name="folder_id" class="material-edit-inline" required>
                    <option value="">-- Выберите папку --</option>
                    <?php foreach ($allFolders as $f): ?>
                        <option value="<?= $f['id'] ?>" <?php echo (isset($_POST['folder_id']) && $_POST['folder_id'] == $f['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($f['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
        </section>

        <section>
            <h3 class="instruct">Материал</h3>
            <textarea name="content" class="material-content-edit" placeholder="Содержание материала"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
        </section>

        <section class="section-margin">
            <p class="material-edit-row">
                <strong>Прикрепить файл:</strong> 
                <input type="file" name="file" class="material-edit-inline">
            </p>
        </section>

        <div class="action-bar-compact">
            <!-- Сохранение -->
            <button type="submit" name="save" class="action-btn" title="Создать материал">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="svg-no-fill">
                    <path d="M20 6L9 17L4 12" stroke="#2A2A2A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
                <span class="tooltip">Создать материал</span>
            </button>

            <!-- Отмена -->
            <a href="../folders/list.php" class="action-btn danger" title="Отмена">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Отмена</span>
            </a>
        </div>
    </form>
    <?php endif; ?>
</main>

<script>
</body>
</html>
