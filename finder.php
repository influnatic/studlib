<?php
require 'finder_logic.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="finder_styles.css">
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
<div class="instruct">Введите теги:</div>
    <form method="GET">
        <input type="text" name="tags" class="search_line" placeholder="Введите #теги через пробел" value="<?php echo htmlspecialchars($query); ?>">
    </form>

    <div class="instruct">Результаты:</div>

    <?php if (empty($search_tags)): ?>
        <div class="no-results">Введите теги для поиска</div>

    <?php elseif (empty($matching_folders) && empty($matching_materials)): ?>
        <div class="no-results">Ничего не найдено по запросу: <?php echo htmlspecialchars($query); ?></div>

    <?php else: ?>
        <!-- Сначала все найденные папки -->
        <?php if (!empty($matching_folders)): ?>
            <div class="section-title">Найденные папки</div>
            <?php foreach ($matching_folders as $folder):
                $color = rand(0,1) ? '#CCA1F7' : '#F6F7A1';
            ?>
                <div class="search-folder-section">
                    <div class="search-folder-header">
                        <div class="search-folder-icon" style="background-color: <?php echo $color; ?>"></div>
                        <div class="search-folder-title"><?php echo htmlspecialchars($folder['name']); ?></div>
                        <a href="folder_view.php?id=<?php echo $folder['id']; ?>" class="search-open-btn">Открыть папку</a>
                    </div>
                    <?php if ($folder['tags']): ?>
                        <p>Теги: <?php echo htmlspecialchars($folder['tags']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Затем все найденные материалы -->
        <?php if (!empty($matching_materials)): ?>
            <div class="section-title">Найденные материалы</div>
            <div class="materials-grid">
                <?php foreach ($matching_materials as $mat):
                $color = rand(0,1) ? '#CCA1F7' : '#F6F7A1'?>
                    <div class="material-card" style="background-color: <?php echo $color; ?>">
                        <h3><?php echo htmlspecialchars($mat['name']); ?></h3>
                        <p>Тип: <?php echo htmlspecialchars($mat['type_name'] ?? 'Не указан'); ?></p>
                        <p>Теги: <?php echo htmlspecialchars($mat['tags']); ?></p>
                        <a href="<?php echo htmlspecialchars($mat['path']); ?>" target="_blank" class="view-link">Просмотреть</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
<footer>
</footer>
</body>
</html>

