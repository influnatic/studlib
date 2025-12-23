<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Неверный ID материала");
}

/* Получаем материал */
$stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->execute([$id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    die("Материал не найден");
}

/* Обработка сохранения */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $tags = isset($_POST['tags']) ? $_POST['tags'] : '';
    $folderName = trim(isset($_POST['folder_name']) ? $_POST['folder_name'] : '');

    $userId = $material['user_id'];

    // Проверяем существует ли папка с таким названием
    $folderStmt = $pdo->prepare("SELECT id FROM folders WHERE user_id = ? AND name = ?");
    $folderStmt->execute([$userId, $folderName]);
    $folder = $folderStmt->fetch(PDO::FETCH_ASSOC);

    if ($folder) {
        $folderId = $folder['id'];
    } else {
        // Создаем новую папку
        $insertFolder = $pdo->prepare("INSERT INTO folders (name, user_id) VALUES (?, ?)");
        $insertFolder->execute([$folderName, $userId]);
        $folderId = $pdo->lastInsertId();
    }

    // Обновляем материал
    $update = $pdo->prepare("
        UPDATE materials 
        SET name = ?, content = ?, tags = ?, folder_id = ?
        WHERE id = ?
    ");

    $update->execute([
            $name,
            $content,
            $tags,
            $folderId,
            $id
    ]);

    header("Location: material_view.php?id=$id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование материала</title>
    <link rel="stylesheet" href="material_view.css">
</head>
<body>

<header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>
        <nav class="header-nav">
            <a href="#">Главная</a>
            <a href="#">Материалы</a>
            <a href="#">Контакты</a>
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
        <a href="#">Главная</a>
        <a href="#">Материалы</a>
        <a href="#">Контакты</a>
    </div>
</header>

<main>
    <h2 class="instruct">Редактирование учебного материала</h2>

    <form class="material-form" method="post">

        <!-- Название -->
        <label>
            Изменить название
            <input type="text" name="name" value="<?= htmlspecialchars($material['name']) ?>" required>
        </label>

        <!-- Папка (текстовое поле) -->
        <label>
            Изменить папку
            <input type="text" name="folder_name" value="<?= htmlspecialchars($material['folder_id'] ?
                    $pdo->query("SELECT name FROM folders WHERE id = ".$material['folder_id'])->fetchColumn() : '') ?>" required>
        </label>

        <!-- Теги -->
        <label>
            Изменить теги
            <input type="text" name="tags" value="<?= htmlspecialchars($material['tags']) ?>">
        </label>

        <!-- Контент -->
        <label>
            Исправить материал
            <textarea name="content" rows="8"><?= htmlspecialchars($material['content']) ?></textarea>
        </label>

        <div class="action-bar-compact">
            <button class="action-btn add" type="submit">
                <svg stroke="currentColor" viewBox="0 0 24 24" fill="none">
                    <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                </svg>
                <span class="tooltip">Сохранить изменения</span>
            </button>

            <button type="button" class="action-btn lift" onclick="history.back()">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Отмена</span>
            </button>
        </div>

    </form>
</main>

<script>
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        mobileNav.classList.toggle('show');
    });
</script>

</body>
</html>
