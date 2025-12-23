<?php
require_once 'db.php';

$userId = 1; // Для примера, пока фиксируем user_id

// Получаем список типов материалов
$typeStmt = $pdo->query("SELECT id, name FROM mat_types");
$types = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

// Создаём папку uploads если не существует
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Обработка формы добавления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $typeId = (int)($_POST['type_id'] ?? 0);
    $tags = trim($_POST['tags'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $folderName = trim($_POST['folder_name'] ?? '');
    $filePath = '';

    // Проверка файла
    if (!empty($_FILES['file']['name'])) {
        $fileName = basename($_FILES['file']['name']);
        $targetPath = 'uploads/' . time() . '_' . $fileName;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $filePath = $targetPath;
        } else {
            $error = "Ошибка при загрузке файла.";
        }
    }

    if (empty($error)) {
        if ($name === '') {
            $error = "Название файла обязательно.";
        } elseif ($typeId <= 0) {
            $error = "Выберите тип материала.";
        } else {
            // Проверяем существует ли папка
            $folderStmt = $pdo->prepare("SELECT id FROM folders WHERE user_id = ? AND name = ?");
            $folderStmt->execute([$userId, $folderName]);
            $folder = $folderStmt->fetch(PDO::FETCH_ASSOC);

            if ($folder) {
                $folderId = $folder['id'];
            } else {
                $insertFolder = $pdo->prepare("INSERT INTO folders (name, user_id) VALUES (?, ?)");
                $insertFolder->execute([$folderName, $userId]);
                $folderId = $pdo->lastInsertId();
            }

            // Добавляем материал
            $stmt = $pdo->prepare("
                INSERT INTO materials (name, type_id, content, folder_id, tags, user_id, path)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $typeId, $content, $folderId, $tags, $userId, $filePath]);

            $newId = $pdo->lastInsertId();
            header("Location: material_view.php?id=$newId&success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание материала</title>
    <link rel="stylesheet" href="material_view.css">
</head>
<body>

<header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>

        <nav class="header-nav">
            <a href="#">Поиск</a>
            <a href="#">Материалы</a>
            <a href="#">Создать</a>
            <a href="#">Чат-бот</a>
        </nav>

        <div class="profile-inline">
            <div class="prof_pic"></div>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <div class="mobile-nav" id="mobileNav">
        <a href="#">Поиск</a>
        <a href="#">Материалы</a>
        <a href="#">Создать</a>
        <a href="#">Чат-бот</a>
    </div>
</header>

<main>
    <h2 class="instruct">Создание учебного материала</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="material-form" method="post" enctype="multipart/form-data">
        <label>
            Название файла:
            <input type="text" name="name" placeholder="Введите название файла" required>
        </label>

        <label>
            Тип материала:
            <select name="type_id" required>
                <option value="">-- Выберите тип --</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Папка:
            <input type="text" name="folder_name" placeholder="Введите название папки" required>
        </label>

        <label>
            Теги:
            <input type="text" name="tags" placeholder="Введите теги через запятую">
        </label>

        <label>
            Текст материала:
            <textarea name="content" rows="6" placeholder="Введите текст конспекта или описание материала"></textarea>
        </label>

        <label>
            Прикрепить файл:
            <input type="file" name="file">
        </label>

        <div class="form-actions">
            <button type="submit" class="action-btn add">
                ➕
                <span class="tooltip">Сохранить материал</span>
            </button>

            <a href="materials_list.php" class="action-btn lift">
                ←
                <span class="tooltip">Отмена</span>
            </a>
        </div>
    </form>
</main>
<script>
    // Кнопка "Развернуть/Свернуть"
    const toggleBtn = document.getElementById('toggleBtn');
    const materialText = document.getElementById('materialText');

    toggleBtn.addEventListener('click', () => {
        materialText.classList.toggle('expanded');
        toggleBtn.textContent = materialText.classList.contains('expanded') ? 'Свернуть' : 'Развернуть';
    });

    // Мобильное меню
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        mobileNav.classList.toggle('show');
    });
</script>

</body>
</html>
