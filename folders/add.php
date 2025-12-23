<?php
require_once '../config/db.php';

$userId = 1; // Пока фиксируем пользователя

$error = '';
$success = '';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName = trim($_POST['folder_name'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    if ($folderName === '') {
        $error = "Название папки обязательно.";
    } else {
        // Проверяем существует ли папка
        $stmt = $pdo->prepare("SELECT id FROM folders WHERE user_id = ? AND name = ?");
        $stmt->execute([$userId, $folderName]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($folder) {
            $error = "Папка с таким названием уже существует.";
        } else {
            // Создаём папку
            $insert = $pdo->prepare("INSERT INTO folders (name, user_id, tags) VALUES (?, ?, ?)");
            $insert->execute([$folderName, $userId, $tags]);

            $success = "Папка успешно создана!";
            header("Location: ../folders/list.php?success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание папки</title>
    <link rel="stylesheet" href="../assets/css/materials.css">
</head>
<body>

<header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>

        <nav class="header-nav">
            <a href="../search/index.php">Поиск</a>
            <a href="../folders/list.php">Материалы</a>
            <a href="../materials/add.php">Создать</a>
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
        <a href="../search/index.php">Поиск</a>
        <a href="../folders/list.php">Материалы</a>
        <a href="../materials/add.php">Создать</a>
        <a href="#">Чат-бот</a>
    </div>
</header>

<main>
    <h2 class="instruct">Создание новой папки</h2>

    <?php if ($error): ?>
        <p style="color:red; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="material-form" method="post">
        <label>
            Название папки:
            <input type="text" name="folder_name" placeholder="Введите название папки" required>
        </label>

        <label>
            Теги папки:
            <input type="text" name="tags" placeholder="Введите теги через запятую">
        </label>

        <div class="form-actions">
            <button type="submit" class="action-btn add">
                ➕
                <span class="tooltip">Создать папку</span>
            </button>

            <a href="../folders/list.php" class="action-btn lift">
                ←
                <span class="tooltip">Отмена</span>
            </a>
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
