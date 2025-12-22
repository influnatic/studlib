<?php
/* Имитация старых данных */
$old_title = 'Конспект по математике';
$old_subject = 'Математика';
$old_topic = 'Производные';
$old_tags = 'анализ, производная, экзамен';
$old_content = 'Здесь может отображаться текст конспекта или расшифровка аудио.';

/* Обработка формы */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $topic = trim($_POST['topic'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $content = trim($_POST['content'] ?? '');

    $title = $title !== '' ? $title : $old_title;
    $subject = $subject !== '' ? $subject : $old_subject;
    $topic = $topic !== '' ? $topic : $old_topic;
    $tags = $tags !== '' ? $tags : $old_tags;
    $content = $content !== '' ? $content : $old_content;

    // 🔜 Здесь будет UPDATE в БД

    // После "сохранения" — редирект на просмотр материала
    header("Location: material_view.php?success=1");
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

<main>
    <h2 class="instruct">Редактирование учебного материала</h2>

    <form class="material-form" method="post">
        <section>
            <label>
                <strong>Название</strong><br>
                <input type="text" name="title" value="<?= htmlspecialchars($old_title) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Предмет</strong><br>
                <input type="text" name="subject" value="<?= htmlspecialchars($old_subject) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Тема</strong><br>
                <input type="text" name="topic" value="<?= htmlspecialchars($old_topic) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Теги</strong><br>
                <input type="text" name="tags" value="<?= htmlspecialchars($old_tags) ?>">
            </label>
        </section>

        <section>
            <label>
                <strong>Материал</strong><br>
                <textarea name="content" rows="6"><?= htmlspecialchars($old_content) ?></textarea>
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

</body>
</html>
