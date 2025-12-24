<?php
require_once '../config/db.php';
require '../config/user_avatar.php';
session_start();

$userId = $_SESSION['user_id'] ?? 1; // Пока фиксируем пользователя

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $userId);

$error = '';
$success = '';

// Получаем список доступных иконок
$iconsDir = '../assets/icons/';
$availableIcons = [];
if (is_dir($iconsDir)) {
    $files = scandir($iconsDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
            $availableIcons[] = $file;
        }
    }
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName = trim($_POST['folder_name'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $icon = trim($_POST['icon'] ?? '');

    if ($folderName === '') {
        $error = "Название папки обязательно.";
    } elseif ($icon === '') {
        $error = "Выберите иконку для папки.";
    } else {
        // Проверяем существует ли папка
        $stmt = $pdo->prepare("SELECT id FROM folders WHERE user_id = ? AND name = ?");
        $stmt->execute([$userId, $folderName]);
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($folder) {
            $error = "Папка с таким названием уже существует.";
        } else {
            // Создаём папку
            $insert = $pdo->prepare("INSERT INTO folders (name, user_id, tags, icon) VALUES (?, ?, ?, ?)");
            $insert->execute([$folderName, $userId, $tags, $icon]);

            $success = "Папка успешно создана!";
            header("Location: ../folders/list.php?success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание папки — StudLib</title>
    <link rel="stylesheet" href="../assets/css/materials.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

<main>
    <?php if (!empty($error)): ?>
        <p class="error-message">
            ✗ <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="form-centered">
        <div class="folder-section">
            <div class="folder-header">
                <!-- Иконка с выбором -->
                <div class="folder-icon-edit-wrapper">
                    <div class="folder-icon" id="mainIcon">
                        <?php 
                        $defaultIcon = !empty($_POST['icon']) ? $_POST['icon'] : ($availableIcons[0] ?? '');
                        if ($defaultIcon): ?>
                            <img src="../assets/icons/<?php echo htmlspecialchars($defaultIcon); ?>" alt="Иконка папки">
                        <?php endif; ?>
                    </div>
                    <div class="icon-selector-compact">
                        <?php foreach ($availableIcons as $iconFile): ?>
                            <label class="icon-option">
                                <?php 
                                $isChecked = false;
                                if (isset($_POST['icon']) && $_POST['icon'] == $iconFile) {
                                    $isChecked = true;
                                } elseif (!isset($_POST['icon']) && !empty($availableIcons) && $iconFile === $availableIcons[0]) {
                                    $isChecked = true;
                                }
                                ?>
                                <input type="radio" name="icon" value="<?php echo htmlspecialchars($iconFile); ?>" <?php echo $isChecked ? 'checked' : ''; ?> required>
                                <div class="icon-preview">
                                    <img src="../assets/icons/<?php echo htmlspecialchars($iconFile); ?>" alt="<?php echo htmlspecialchars(pathinfo($iconFile, PATHINFO_FILENAME)); ?>">
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Название и теги -->
                <div class="folder-info-wrapper">
                    <input type="text" name="folder_name" value="<?php echo htmlspecialchars($_POST['folder_name'] ?? ''); ?>" class="folder-title-edit" placeholder="Название папки" required>
                    <input type="text" name="tags" value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>" class="tags-input-edit" placeholder="Теги (например, #матан #2курс)">
                </div>
            </div>
        </div>
        
        <!-- Кнопки управления внизу формы -->
        <div class="action-bar-compact">
            <!-- Сохранение -->
            <button type="submit" class="action-btn" title="Создать папку">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="svg-no-fill">
                    <path d="M20 6L9 17L4 12" stroke="#2A2A2A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
                <span class="tooltip">Создать папку</span>
            </button>

            <!-- Отмена -->
            <a href="../folders/list.php" class="action-btn danger" title="Отмена">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="tooltip">Отмена</span>
            </a>
        </div>
    </form>
</main>

<script>

    // Обработка выбора иконки - обновление главной иконки
    const iconInputs = document.querySelectorAll('.icon-selector-compact input[type="radio"]');
    const mainIconDiv = document.getElementById('mainIcon');
    
    iconInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked && mainIconDiv) {
                const iconSrc = this.value;
                let img = mainIconDiv.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    mainIconDiv.appendChild(img);
                }
                img.src = '../assets/icons/' + iconSrc;
                img.alt = iconSrc.replace('.svg', '');
            }
        });
        
        // Инициализация при загрузке
        if (input.checked && mainIconDiv) {
            const iconSrc = input.value;
            let img = mainIconDiv.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                mainIconDiv.appendChild(img);
            }
            img.src = '../assets/icons/' + iconSrc;
            img.alt = iconSrc.replace('.svg', '');
        }
    });
</script>

</body>
</html>
