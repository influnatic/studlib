<?php
require 'finder_logic.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>
        <nav class="header-nav">
            <a href="finder.php">Поиск</a>
            <a href="folder_look.php">Материалы</a>
            <div class="dropdown">
                            <!-- Скрытый чекбокс -->
                            <input type="checkbox" id="add-dropdown" class="dropdown-checkbox">
                            <!-- Кнопка как label для чекбокса -->
                            <label for="add-dropdown" class="dropdown-toggle">
                                Создать
                            </label>
                            <!-- Меню -->
                            <ul class="dropdown-menu">
                                <li><a href="#">Создать папку</a></li>
                                <li><a href="#">Создать документ</a></li>
                            </ul>
                        </div>
            <a href="https://web.telegram.org/k/">Чат-бот</a>

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
        <a href="finder.php">Поиск</a>
        <a href="folder_look.php">Материалы</a>
        <div class="dropdown">
                    <!-- Скрытый чекбокс -->
                    <input type="checkbox" id="add-dropdown-mobile" class="dropdown-checkbox">
                    <!-- Кнопка как label для чекбокса -->
                    <label for="add-dropdown-mobile" class="dropdown-toggle">
                        Создать
                    </label>
                    <!-- Меню -->
                    <ul class="dropdown-menu">
                        <li><a href="#">Создать папку</a></li>
                        <li><a href="#">Создать документ</a></li>
                    </ul>
                </div>

        <a href="https://web.telegram.org/k/">Чат-бот</a>

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
                    <div class="folder-header">
                        <div class="folder-icon" style="background-color: <?php echo $color; ?>"></div>
                        <div class="folder-title"><?php echo htmlspecialchars($folder['name']); ?></div>
                        <a href="folder_view.php?id=<?php echo $folder['id']; ?>" class="open-folder-btn">Открыть папку</a>
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
                        <a href="material_view.php?id=<?php echo $mat['id']; ?>" class="view-link">Просмотреть</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
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

