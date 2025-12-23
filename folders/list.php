<?php
require '../config/db.php';  // подключение к базе
session_start();
$user_id = 1;  // для теста, потом $_SESSION['user_id']

// Получаем все папки пользователя
$stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ? ORDER BY name");
$stmt->execute([$user_id]);
$folders = $stmt->fetchAll();

// Получаем все материалы с типом
$stmt = $pdo->prepare("
    SELECT m.*, mt.name AS type_name
    FROM materials m
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.user_id = ?
");
$stmt->execute([$user_id]);
$all_materials = $stmt->fetchAll();

// Группируем материалы по folder_id
$materials_by_folder = [];
foreach ($all_materials as $mat) {
    $fid = $mat['folder_id'] ?? 0;  // если null — в корне, но у тебя все в папках
    $materials_by_folder[$fid][] = $mat;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>


<header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>
        <nav class="header-nav">
            <a href="../search/index.php">Поиск</a>
            <a href="../folders/list.php">Материалы</a>
            <div class="dropdown">
                            <!-- Скрытый чекбокс -->
                            <input type="checkbox" id="add-dropdown" class="dropdown-checkbox">
                            <!-- Кнопка как label для чекбокса -->
                            <label for="add-dropdown" class="dropdown-toggle">
                                Создать
                            </label>
                            <!-- Меню -->
                            <ul class="dropdown-menu">
                                <li><a href="../folders/add.php">Создать папку</a></li>
                                <li><a href="../materials/add.php">Создать документ</a></li>
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
        <a href="../search/index.php">Поиск</a>
        <a href="../folders/list.php">Материалы</a>
        <div class="dropdown">
                    <!-- Скрытый чекбокс -->
                    <input type="checkbox" id="add-dropdown-mobile" class="dropdown-checkbox">
                    <!-- Кнопка как label для чекбокса -->
                    <label for="add-dropdown-mobile" class="dropdown-toggle">
                        Создать
                    </label>
                    <!-- Меню -->
                    <ul class="dropdown-menu">
                        <li><a href="../folders/add.php">Создать папку</a></li>
                        <li><a href="../materials/add.php">Создать документ</a></li>
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
                <a href="../folders/view.php?id=<?php echo $folder['id']; ?>" class="open-folder-btn">Открыть папку</a>
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
                        <a href="../materials/view.php?id=<?php echo $mat['id']; ?>" class="view-link">Просмотреть</a>
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

