<?php
require 'folder_look_logic.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
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
<?php foreach ($folders as $folder):
        $color = rand(0,1) ? '#CCA1F7' : '#F6F7A1';
        $folder_mats = $materials_by_folder[$folder['id']] ?? [];
    ?>

        <div class="folder-section">
            <div class="folder-header">
                <div class="folder-icon" style="background-color: <?php echo $color; ?>"></div>
                <div class="folder-title"><?php echo htmlspecialchars($folder['name']); ?></div>
                <a href="folder_view.php?id=<?php echo $folder['id']; ?>" class="open-folder-btn">Открыть папку</a>
            </div>

        <?php if (!empty($folder_mats)): ?>
            <div class="materials-grid">
                <?php foreach ($folder_mats as $mat):
                    // Рандомный цвет фона для карточки
                    $bg_color = rand(0,1) ? '#CCA1F7' : '#F6F7A1';
                ?>
                    <div class="material-card" style="background-color: <?php echo $bg_color; ?>;">
                        <h3><?php echo htmlspecialchars($mat['name']); ?></h3>
                        <p>Тип: <?php echo htmlspecialchars($mat['type_name'] ?? 'Не указан'); ?></p>
                        <p>Теги: <?php echo htmlspecialchars($mat['tags']); ?></p>
                        <a href="material_view.php?id=<?php echo $mat['id']; ?>" class="view-link">Просмотреть</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-folder">
                В папке пока нет материалов
            </div>
        <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (empty($folders)): ?>
        <p class="empty_message">У вас пока нет папок. Добавьте первую!</p>
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