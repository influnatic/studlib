<?php
// Здесь позже будет обработка формы и сохранение в БД
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

<!-- ===== Header ===== -->
<header class="main-header">
    <div class="header-row">

        <div class="header-title">StudLib</div>

        <nav class="header-nav">
            <a href="#">Материалы</a>
            <a href="#">Чат-бот</a>
            <a href="#">Поиск</a>
        </nav>

        <div class="profile-inline">
            <div class="prof_pic"></div>
            <span class="profile-name">Alex</span>
        </div>

    </div>
</header>

<!-- ===== Main ===== -->
<main>

    <h2 class="instruct">Добавление учебного материала</h2>

    <!-- ===== Форма добавления ===== -->
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

        <!-- ===== Кнопки ===== -->
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

</body>
</html>
