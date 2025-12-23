<?php
session_start();
require 'config/db.php';

// Если пользователь авторизован, перенаправляем в кабинет или к материалам
if (isset($_SESSION['user_id'])) {
    header("Location: folders/list.php");
    exit;
}

// Если не авторизован, перенаправляем на страницу входа
header("Location: auth/login.php");
exit;
?>

