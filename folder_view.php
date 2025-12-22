<?php
require 'folder_view_logic.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="folderview_styles.css">
</head>
<body>
<header>
    <div class="logo">StudLib</div>
    <div class="header_toolbar">
        <div class="prof_pic"></div>
    <div class="nav">
        <a href="folder_look.php">Мои материалы</a>
            <!-- Кнопка с dropdown -->
        <div class="dropdown">
        <!-- Скрытый чекбокс -->
        <input type="checkbox" id="add-dropdown" class="dropdown-checkbox">
        <!-- Кнопка как label для чекбокса -->
        <label for="add-dropdown" class="dropdown-toggle">
            Добавить
        </label>
        <!-- Меню -->
        <ul class="dropdown-menu">
            <li><a href="#">Добавить папку</a></li>
            <li><a href="#">Добавить документ</a></li>
        </ul>
        </div>
            <a href="finder.php">Поиск</a>
            <a href="https://web.telegram.org/k/">Перейти в чат</a>
    </div>
    </div>

</header>
<main>
    <h1 class="folder-title-big"><?php echo htmlspecialchars($folder['name']); ?></h1>

    <div class="buttons">
        <a href="folder_edit.php?id=<?php echo $folder['id']; ?>" class="button_edit">Редактировать</a>
        <btn class="button_delete">Удалить</btn>
        <div class="dropdown share-dropdown">
            <!-- Скрытый чекбокс -->
            <input type="checkbox" id="share-dropdown" class="dropdown-checkbox">

            <!-- Кнопка как label -->
            <label for="share-dropdown" class="button_copy">
                Поделиться
            </label>

            <!-- Выпадающее меню с ссылкой -->
            <div class="dropdown-menu share-menu">
                <p class="dropdown-menu-text">
                    Скопируйте ссылку:
                </p>
                <input type="text"
                       value="http://localhost/studlib/folder_view.php?id=<?php echo $folder_id; ?>"
                       readonly
                       class="share-link-input">
            </div>
        </div>
    </div>
    <?php if (!empty($folder['tags'])): ?>
        <p class="tags_folder">
            Теги: <?php echo htmlspecialchars($folder['tags']); ?>
        </p>
    <?php endif; ?>


    <?php if (!empty($materials)): ?>
        <div class="materials-grid">
            <?php foreach ($materials as $mat):
                $bg_color = rand(0,1) ? '#CCA1F7' : '#F6F7A1';
            ?>
                <div class="material-card" style="background-color: <?php echo $bg_color; ?>;">
                    <h3><?php echo htmlspecialchars($mat['name']); ?></h3>
                    <p>Тип: <?php echo htmlspecialchars($mat['type_name'] ?? 'Не указан'); ?></p>
                    <?php if ($mat['tags']): ?>
                        <p>Теги: <?php echo htmlspecialchars($mat['tags']); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo htmlspecialchars($mat['path']); ?>" target="_blank" class="view-link">
                        Просмотреть
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-message">
            В этой папке пока нет материалов.<br>
            Добавьте первый документ через кнопку "Добавить" в шапке!
        </div>
    <?php endif; ?>

</main>
</body>
</html>