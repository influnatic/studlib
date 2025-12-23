<?php
require 'db.php';

// Получаем ID материала из GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Неверный ID материала");

// Получаем текущий материал
$stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->execute([$id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$material) die("Материал не найден");

// Предположим, что у пользователя есть ID (например, user_id = 1)
$userId = $material['user_id'] ?? 1;

// Получаем ID предыдущего материала (меньше текущего ID)
$prevStmt = $pdo->prepare("
    SELECT id FROM materials 
    WHERE user_id = ? AND id < ? 
    ORDER BY id DESC LIMIT 1
");
$prevStmt->execute([$userId, $id]);
$previous = $prevStmt->fetch(PDO::FETCH_ASSOC);
$previousId = $previous['id'] ?? null;

// Получаем ID следующего материала (больше текущего ID)
$nextStmt = $pdo->prepare("
    SELECT id FROM materials 
    WHERE user_id = ? AND id > ? 
    ORDER BY id ASC LIMIT 1
");
$nextStmt->execute([$userId, $id]);
$next = $nextStmt->fetch(PDO::FETCH_ASSOC);
$nextId = $next['id'] ?? null;
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
            <a href="finder.php">Поиск</a>
            <a href="folder_look.php">Материалы</a>
            <a href="material_add.php">Создать</a>
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
    <div style="margin-bottom:10px; font-size: smaller; margin-top: 0">
        <a href="folder_look.php" class="back-btn lift">
            ← Назад к папкам
        </a>
    </div>

    <?php if ($show_success): ?>
        <p style="color:#77784E; margin-bottom:20px;">
            ✔ Изменения сохранены
        </p>
    <?php endif; ?>

    <h2 class="instruct">Просмотр учебного материала</h2>

    <section>
        <p><strong>Название:</strong> <?= htmlspecialchars($material['name']) ?></p>
        <p><strong>Папка:</strong> <?= htmlspecialchars($material['folder_name']) ?></p>
        <p><strong>Формат материала:</strong> <?= htmlspecialchars($material['type_name']) ?></p>
        <p><strong>Теги:</strong> <?= htmlspecialchars($material['tags']) ?></p>
    </section>

    <section>
        <br>
        <h3 class="instruct">Материал</h3>

        <p>
            <a href="<?= htmlspecialchars($material['path']) ?>" target="_blank">📎 Открыть прикреплённый файл</a>
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

    <div class="action-bar-compact">
        <!-- Предыдущий материал -->
        <form action="material_view.php" method="get">
            <input type="hidden" name="id" value="<?= $previousId ?? $material['id'] ?>">
            <button class="action-btn prev" type="submit">
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2893 5.70708C13.8988 5.31655 13.2657 5.31655 12.8751 5.70708L7.98768 10.5993C7.20729 11.3805 7.2076 12.6463 7.98837 13.427L12.8787 18.3174C13.2693 18.7079 13.9024 18.7079 14.293 18.3174C14.6835 17.9269 14.6835 17.2937 14.293 16.9032L10.1073 12.7175C9.71678 12.327 9.71678 11.6939 10.1073 11.3033L14.2893 7.12129C14.6799 6.73077 14.6799 6.0976 14.2893 5.70708Z" fill="#0F0F0F"/>
                </svg>
                <span class="tooltip">Предыдущий материал</span>
            </button>
        </form>

        <!-- Удаление -->
        <form action="material_delete.php" method="post">
            <input type="hidden" name="id" value="<?= $material['id'] ?>">
            <button class="action-btn danger" type="submit">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M10 12L14 16M14 12L10 16M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Удалить материал</span>
            </button>
        </form>

        <!-- Редактирование -->
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
                <svg width="800px" height="800px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">

                                <line fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" x2="12" y1="19" y2="5"/>

                                <line fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="5" x2="19" y1="12" y2="12"/>

                </svg>
                <span class="tooltip">Добавить материал</span>
            </button>
        </form>


        <!-- Поделиться -->
        <button class="action-btn share" onclick="navigator.clipboard.writeText(window.location.href)">
            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 13V17.5C20 20.5577 16 20.5 12 20.5C8 20.5 4 20.5577 4 17.5V13M12 3L12 15M12 3L16 7M12 3L8 7" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="tooltip">Скопировать ссылку</span>
        </button>

        <!-- Следующий материал -->
        <form action="material_view.php" method="get">
            <input type="hidden" name="id" value="<?= $nextId ?? $material['id'] ?>">
            <button class="action-btn next" type="submit">
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="#0F0F0F"/>
                </svg>
                <span class="tooltip">Следующий материал</span>
            </button>
        </form>

    </div>

    </div>
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
