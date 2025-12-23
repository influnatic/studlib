<?php
require 'db.php'; // подключение к БД

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1; // по умолчанию 1

$stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->execute([$id]);
$material = $stmt->fetch();

if (!$material) {
    die("Материал не найден");
}

$show_success = isset($_GET['success']) && $_GET['success'] == 1;
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLib — Просмотр материала</title>
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
    <?php if ($show_success): ?>
        <p style="color:#77784E; margin-bottom:20px;">
            ✔ Изменения сохранены
        </p>
    <?php endif; ?>

    <h2 class="instruct">Просмотр учебного материала</h2>

    <!-- Информация -->
    <section>
        <p><strong>Название:</strong> <?= htmlspecialchars($material['title']) ?></p>
        <p><strong>Предмет:</strong> <?= htmlspecialchars($material['subject']) ?></p>
        <p><strong>Тема:</strong> <?= htmlspecialchars($material['topic']) ?></p>
        <p><strong>Теги:</strong> <?= htmlspecialchars($material['tags']) ?></p>
    </section>

    <!-- Материал -->
    <section>
        <br>
        <h3 class="instruct">Материал</h3>

        <p>
            <a href="#">📎 Открыть прикреплённый файл</a>
        </p>

        <div class="detail-item">
            <div class="detail-value">
                <div class="material-text collapsed" id="materialText">
                    <?= nl2br(htmlspecialchars($material['content'])) ?>
                </div>
                <button class="toggle-btn" id="toggleBtn">Развернуть</button>
            </div>
        </div>
    </section>

    <!-- ===== Панель действий ===== -->
    <div class="action-bar-compact">

        <!-- Удалить -->
        <form action="material_delete.php" method="post">
            <input type="hidden" name="id" value="<?= $material['id'] ?>">
            <button class="action-btn danger" type="submit">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M10 12L14 16M14 12L10 16M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Удалить материал</span>
            </button>
        </form>

        <!-- Редактировать -->
        <form action="material_edit.php" method="get">
            <input type="hidden" name="id" value="<?= $material['id'] ?>">
            <button class="action-btn rotate" type="submit">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4H7.2C6.0799 4 5.51984 4 5.09202 4.21799C4.71569 4.40974 4.40973 4.7157 4.21799 5.09202C4 5.51985 4 6.0799 4 7.2V16.8C4 17.9201 4 18.4802 4.21799 18.908C4.40973 19.2843 4.71569 19.5903 5.09202 19.782C5.51984 20 6.0799 20 7.2 20H16.8C17.9201 20 18.4802 20 18.908 19.782C19.2843 19.5903 19.5903 19.2843 19.782 18.908C20 18.4802 20 17.9201 20 16.8V12.5M15.5 5.5L18.3284 8.32843M10.7627 10.2373L17.411 3.58902C18.192 2.80797 19.4584 2.80797 20.2394 3.58902C21.0205 4.37007 21.0205 5.6364 20.2394 6.41745L13.3774 13.2794C12.6158 14.0411 12.235 14.4219 11.8012 14.7247C11.4162 14.9936 11.0009 15.2162 10.564 15.3882C10.0717 15.582 9.54378 15.6885 8.48793 15.9016L8 16" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Редактировать материал</span>
            </button>
        </form>

        <!-- Добавить -->
        <form action="material_add.php" method="get">
            <button class="action-btn add" type="submit">
                <svg stroke="currentColor" viewBox="0 0 24 24" fill="none">
                    <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                </svg>
                <span class="tooltip">Добавить материал</span>
            </button>
        </form>

    </div>
</main>

<script>
    const toggleBtn = document.getElementById('toggleBtn');
    const materialText = document.getElementById('materialText');

    toggleBtn.addEventListener('click', () => {
        materialText.classList.toggle('expanded');

        toggleBtn.textContent = materialText.classList.contains('expanded') ? 'Свернуть' : 'Развернуть';
    });

    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        mobileNav.classList.toggle('show');
    });
</script>

</body>
</html>
