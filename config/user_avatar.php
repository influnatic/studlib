<?php
// Функция для получения аватара пользователя из БД
function getUserAvatar($pdo, $user_id) {
    if (!$user_id) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        return $user['avatar'] ?? null;
    } catch (PDOException $e) {
        return null;
    }
}
?>

