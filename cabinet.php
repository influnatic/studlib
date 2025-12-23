<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$display_name = $_SESSION['display_name'] ?? 'User';
$username = $_SESSION['username'] ?? 'Неизвестно';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLib — Личный кабинет</title>
    <link rel="stylesheet" href="material_view.css">
</head>
    <header class="main-header">
    <div class="header-row">
        <div class="header-title">StudLib</div>
        <nav class="header-nav">
            <a href="finder.php">Поиск</a>
            <a href="material_view.php">Материалы</a>
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


    <!-- Личный кабинет -->
    <div class="cabinet-content">
        <h1 class="cabinet-greeting">Привет, <?= htmlspecialchars($display_name) ?>! 👋</h1>
        <p style="font-size:18px; color:#666;">Добро пожаловать в личный кабинет</p>
        <link rel="stylesheet" href="cabinet.css">
        <div class="cabinet-info">
            <div class="info-block">
                <div class="info-label">Логин</div>
                <div class="info-value"><?= htmlspecialchars($username) ?></div>
            </div>
            <div class="info-block">
                <div class="info-label">Роль</div>
                <div class="info-value">Студент</div>
            </div>
            <div class="info-block">
                <div class="info-label">Дата регистрации</div>
                <div class="info-value">23.12.2025</div>
            </div>
            <div class="info-block">
                <div class="info-label">Дней подряд</div>
                <div class="info-value">7 Дней 🔥</div>
            </div>
        </div>

        <div class="cabinet-actions">
            <a href="material_view.php" class="btn btn-back">Перейти к материалам</a>
            <a href="logout.php" class="btn btn-logout">Выйти из аккаунта</a>
        </div>
    </div>

</body>
</html>