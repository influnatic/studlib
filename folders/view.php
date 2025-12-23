<?php
require '../config/db.php';  // подключение к базе
session_start();
$user_id = 1;  // Для теста. Потом замени на $_SESSION['user_id'] после авторизации

// Получаем id папки из URL
$folder_id = $_GET['id'] ?? null;

if (!$folder_id || !is_numeric($folder_id)) {
    die("Ошибка: не указана папка");
}

// Получаем информацию о папке
$stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ? AND user_id = ?");
$stmt->execute([$folder_id, $user_id]);
$folder = $stmt->fetch();

if (!$folder) {
    die("Папка не найдена или доступ запрещён");
}

// Получаем материалы только из этой папки
$stmt = $pdo->prepare("
    SELECT m.*, mt.name AS type_name
    FROM materials m
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.folder_id = ? AND m.user_id = ?
");
$stmt->execute([$folder_id, $user_id]);
$materials = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($folder['name']); ?> — StudLib</title>
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
    <h1 class="folder-title-big"><?php echo htmlspecialchars($folder['name']); ?></h1>

    <?php if (!empty($folder['tags'])): ?>
        <p class="tags_folder">
            Теги: <?php echo htmlspecialchars($folder['tags']); ?>
        </p>
    <?php endif; ?>

    <div class="buttons">
        <a href="../folders/edit.php?id=<?php echo $folder['id']; ?>" class="button_edit">Редактировать</a>
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
                       value="http://localhost/studlib/folders/view.php?id=<?php echo $folder_id; ?>"
                       readonly
                       class="share-link-input">
            </div>
        </div>
    </div>

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
                    <a href="../materials/view.php?id=<?php echo $mat['id']; ?>" class="view-link">
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

