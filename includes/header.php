<?php
/**
 * Общий хедер для всех страниц
 */

// Подключаем необходимые файлы
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/db.php';
}
require_once __DIR__ . '/../config/user_avatar.php';

// Получаем ID пользователя
$user_id = $user_id ?? $_SESSION['user_id'] ?? null;

// Получаем аватар
$user_avatar = $user_id ? getUserAvatar($pdo, $user_id) : null;

// Определяем базовый путь
$scriptPath = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptPath);
// Если директория не корневая (не '/' и не '.'), значит мы в подпапке
$basePath = ($scriptDir !== '/' && $scriptDir !== '.' && $scriptDir !== '\\') ? '../' : '';

// Путь к кабинету
$cabinetPath = strpos($_SERVER['SCRIPT_NAME'], 'cabinet/') !== false ? 'index.php' : $basePath . 'cabinet/index.php';
?>

<header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>
        <nav class="header-nav">
            <a href="<?= $basePath ?>search/index.php">Поиск</a>
            <a href="<?= $basePath ?>folders/list.php">Материалы</a>
            <div class="dropdown">
                <input type="checkbox" id="add-dropdown" class="dropdown-checkbox">
                <label for="add-dropdown" class="dropdown-toggle">Создать</label>
                <ul class="dropdown-menu">
                    <li><a href="<?= $basePath ?>folders/add.php">Создать папку</a></li>
                    <li><a href="<?= $basePath ?>materials/add.php">Создать документ</a></li>
                </ul>
            </div>
            <a href="https://web.telegram.org/k/">Чат-бот</a>
        </nav>

        <div class="profile-inline">
            <a href="<?= $cabinetPath ?>" class="prof_pic" title="Личный кабинет">
                <?php if (!empty($user_avatar)): ?>
                    <img src="<?= $basePath ?>assets/avatars/<?= htmlspecialchars($user_avatar) ?>" alt="Аватар">
                <?php endif; ?>
            </a>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <div class="mobile-nav" id="mobileNav">
        <a href="<?= $basePath ?>search/index.php">Поиск</a>
        <a href="<?= $basePath ?>folders/list.php">Материалы</a>
        <div class="dropdown">
            <input type="checkbox" id="add-dropdown-mobile" class="dropdown-checkbox">
            <label for="add-dropdown-mobile" class="dropdown-toggle">Создать</label>
            <ul class="dropdown-menu">
                <li><a href="<?= $basePath ?>folders/add.php">Создать папку</a></li>
                <li><a href="<?= $basePath ?>materials/add.php">Создать документ</a></li>
            </ul>
        </div>
        <a href="https://web.telegram.org/k/">Чат-бот</a>
    </div>
</header>

<script>
    // Скрипт для hamburger меню
    (function() {
        const hamburger = document.getElementById('hamburger');
        const mobileNav = document.getElementById('mobileNav');

        if (hamburger && mobileNav) {
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                mobileNav.classList.toggle('show');
            });
        }
    })();
</script>

