<?php
require_once 'db.php'; // Подключение к БД

// Обработка формы добавления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $topic = $_POST['topic'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $content = $_POST['content'] ?? '';

    if (trim($title) === '') {
        $error = "Название материала обязательно.";
    } else {
        // Вставка в базу
        $stmt = $pdo->prepare("
            INSERT INTO materials (title, subject, topic, tags, content)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $subject, $topic, $tags, $content]);

        // Получаем ID нового материала
        $newId = $pdo->lastInsertId();

        // Перенаправляем на просмотр
        header("Location: material_view.php?id=$newId&success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLib — Добавление материала</title>
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
    <h2 class="instruct">Добавление учебного материала</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="material-form" action="material_add.php" method="post">
        <label>
            Название:
            <input type="text" name="title" placeholder="Конспект по математике" required>
        </label>

        <label>
            Предмет:
            <input type="text" name="subject" placeholder="Математика" required>
        </label>

        <label>
            Тема:
            <input type="text" name="topic" placeholder="Производные" required>
        </label>

        <label>
            Теги:
            <input type="text" name="tags" placeholder="анализ, производная, экзамен">
        </label>

        <label>
            Текст материала:
            <textarea name="content" rows="6" placeholder="Введите текст конспекта или описание материала"></textarea>
        </label>

        <div class="form-actions">
            <button type="submit" class="action-btn add">
                ➕
                <span class="tooltip">Сохранить материал</span>
            </button>

            <a href="material_view.php" class="action-btn lift">
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
