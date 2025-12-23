<?php
require_once 'db.php'; // Подключение к БД

// Получаем ID материала из GET-параметра
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Неверный ID материала.");
}

// Получаем данные материала из базы
$stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->execute([$id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    die("Материал не найден.");
}

// Обработка формы обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $topic = $_POST['topic'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $content = $_POST['content'] ?? '';

    // Обновляем запись в БД
    $update = $pdo->prepare("
        UPDATE materials SET
            title = ?, subject = ?, topic = ?, tags = ?, content = ?
        WHERE id = ?
    ");
    $update->execute([$title, $subject, $topic, $tags, $content, $id]);

    // Перенаправляем обратно на просмотр с сообщением об успехе
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
        <section>
            <label>
                <strong>Название</strong><br>
                <input type="text" name="title" value="<?= htmlspecialchars($material['title']) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Предмет</strong><br>
                <input type="text" name="subject" value="<?= htmlspecialchars($material['subject']) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Тема</strong><br>
                <input type="text" name="topic" value="<?= htmlspecialchars($material['topic']) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Теги</strong><br>
                <input type="text" name="tags" value="<?= htmlspecialchars($material['tags']) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Материал</strong><br>
                <textarea name="content" rows="6"><?= htmlspecialchars($material['content']) ?></textarea>
            </label>
        </section>

        <div class="action-bar-compact">
            <!-- Кнопка сохранить -->
            <button class="action-btn add" type="submit">
                <svg stroke="currentColor" viewBox="0 0 24 24" fill="none">
                    <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                          stroke-width="2"
                          stroke-linejoin="round"
                          stroke-linecap="round"></path>
                </svg>
                <span class="tooltip">Сохранить изменения</span>
            </button>

            <!-- Кнопка отмена -->
            <button type="button" class="action-btn lift" onclick="history.back()">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
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
