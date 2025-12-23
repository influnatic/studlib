<?php
session_start();
require '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../folders/list.php");
    exit;
}

// Сбрасываем ошибку при простом заходе на страницу (GET)
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    sleep(1); // задержка от брута
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        $error = "⚠️ Заполни логин и пароль!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['display_name'] = $user['display_name'];
            header("Location: ../folders/list.php");
            exit;
        } else {
            $error = "🚫 Неверный логин или пароль. Попробуй ещё раз!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход | STUDYB</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="box">
        <div class="header">STUDLIB</div>
        <h2>Вход</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Логин" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <a href="https://telegram.me/BotFather" target="_blank">
            <button type="button" class="register-btn">Регистрация через <br> Telegram-Бот</button>
        </a>
        <p style="margin-top:20px; color:#888; font-size:14px; font-family:'Comfortaa', cursive;">
            Материалы чат-бот поиск © 2025
        </p>
    </div>
</body>
</html>