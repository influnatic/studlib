<?php
require 'db.php';
session_start();
$user_id = 1;  // потом $_SESSION['user_id']

$query = $_GET['tags'] ?? '';
$search_tags = [];

if (!empty($query)) {
    $tags_input = str_replace('#', ' ', $query);
    $tags_array = array_filter(array_map('trim', explode(' ', $tags_input)));
    $search_tags = array_map('strtolower', $tags_array);
}

$matching_folders = [];
$matching_materials = [];

if (!empty($search_tags)) {
    $like_conditions = [];
    $params = [$user_id];
    foreach ($search_tags as $i => $tag) {
        $like_conditions[] = "LOWER(tags) LIKE ?";
        $params[] = '%' . $tag . '%';
    }
    $where = implode(' OR ', $like_conditions);

    // Найденные папки
    $stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ? AND ($where) ORDER BY name");
    $stmt->execute($params);
    $matching_folders = $stmt->fetchAll();

    // Найденные материалы
    $stmt = $pdo->prepare("
        SELECT m.*, mt.name AS type_name
        FROM materials m
        LEFT JOIN mat_types mt ON m.type_id = mt.id
        WHERE m.user_id = ? AND ($where)
        ORDER BY m.name
    ");
    $stmt->execute($params);
    $matching_materials = $stmt->fetchAll();
}
?>