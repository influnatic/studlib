<?php
require '../config/db.php';
session_start();
$user_id = 1;  // Для теста. Потом $_SESSION['user_id']

// Получаем id папки
$folder_id = $_GET['id'] ?? null;
if (!$folder_id || !is_numeric($folder_id)) {
    die("Ошибка: не указана папка");
}

// Обработка сохранения и перемещения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $new_name = trim($_POST['folder_name'] ?? '');
        $new_tags = trim($_POST['folder_tags'] ?? '');
        if ($new_name !== '') {
            $stmt = $pdo->prepare("UPDATE folders SET name = ?, tags = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$new_name, $new_tags, $folder_id, $user_id]);
        }
        header("Location: ../folders/view.php?id=$folder_id");
        exit;
    }

    if (isset($_POST['move']) && !empty($_POST['selected_materials']) && !empty($_POST['target_folder'])) {
        $target = $_POST['target_folder'];
        $selected = $_POST['selected_materials'];
        $placeholders = str_repeat('?,', count($selected) - 1) . '?';
        $stmt = $pdo->prepare("UPDATE materials SET folder_id = ? WHERE id IN ($placeholders) AND user_id = ?");
        $params = array_merge([$target], $selected, [$user_id]);
        $stmt->execute($params);
        header("Location: ../folders/edit.php?id=$folder_id");  // остаёмся в редактировании
        exit;
    }
}

// Получаем данные папки
$stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ? AND user_id = ?");
$stmt->execute([$folder_id, $user_id]);
$folder = $stmt->fetch();

if (!$folder) {
    die("Папка не найдена или доступ запрещён");
}

// Получаем материалы в папке
$stmt = $pdo->prepare("
    SELECT m.*, mt.name AS type_name
    FROM materials m
    LEFT JOIN mat_types mt ON m.type_id = mt.id
    WHERE m.folder_id = ? AND m.user_id = ?
");
$stmt->execute([$folder_id, $user_id]);
$materials = $stmt->fetchAll();

// Получаем другие папки для перемещения
$stmt = $pdo->prepare("SELECT id, name FROM folders WHERE user_id = ? AND id != ? ORDER BY name");
$stmt->execute([$user_id, $folder_id]);
$other_folders = $stmt->fetchAll();
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
     <form method="POST">
            <!-- Поля для редактирования названия и тегов -->
            <div class="edit-fields">
                <label>
                    <span class="label-text">Название папки:</span>
                    <input type="text" name="folder_name" value="<?php echo htmlspecialchars($folder['name']); ?>" class="edit-input" required>
                </label>

                <label>
                    <span class="label-text">Теги (через пробел):</span>
                    <input type="text" name="folder_tags" value="<?php echo htmlspecialchars($folder['tags'] ?? ''); ?>" class="edit-input-tags" placeholder="например: #матан #2курс">
                </label>
            </div>

            <!-- Блок перемещения (если есть материалы) -->
            <?php if (!empty($materials)): ?>
                <div class="move-block">
                    <label>
                        Переместить выбранные материалы в:
                        <select name="target_folder" class="select-folder">
                            <option value="">— Выберите папку —</option>
                            <?php foreach ($other_folders as $f): ?>
                                <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            <?php endif; ?>

            <!-- Материалы с чекбоксами -->
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

                            <label class="checkbox-label">
                                <input type="checkbox" name="selected_materials[]" value="<?php echo $mat['id']; ?>">
                                Выбрать для перемещения
                            </label>

                            <a href="../materials/view.php?id=<?php echo $mat['id']; ?>" class="view-link">Просмотреть</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <!-- Кнопки управления -->
                        <div class="buttons">
                            <button type="submit" name="save" class="button_save">Сохранить</button>

                            <?php if (!empty($materials)): ?>
                                <button type="submit" name="move" class="button_move">Переместить выбранные</button>
                            <?php endif; ?>

                            <a href="../folders/view.php?id=<?php echo $folder_id; ?>" class="button_cancel">Отмена</a>
                        </div>
            <?php else: ?>
                <div class="empty-message">
                    В этой папке пока нет материалов.
                </div>
            <?php endif; ?>
        </form>


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

