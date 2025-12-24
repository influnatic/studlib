<?php
require '../config/db.php';
require '../config/user_avatar.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;  // Для теста. Потом $_SESSION['user_id']

// Получаем аватар пользователя
$user_avatar = getUserAvatar($pdo, $user_id);

// Получаем id папки
$folder_id = $_GET['id'] ?? null;
if (!$folder_id || !is_numeric($folder_id)) {
    die("Ошибка: не указана папка");
}

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

// Обработка сохранения и перемещения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $new_name = trim($_POST['folder_name'] ?? '');
        $new_tags = trim($_POST['folder_tags'] ?? '');
        $new_icon = trim($_POST['icon'] ?? '');
        if ($new_name !== '') {
            if ($new_icon !== '') {
                $stmt = $pdo->prepare("UPDATE folders SET name = ?, tags = ?, icon = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$new_name, $new_tags, $new_icon, $folder_id, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE folders SET name = ?, tags = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$new_name, $new_tags, $folder_id, $user_id]);
            }
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
$stmt = $pdo->prepare("SELECT id, name, icon FROM folders WHERE user_id = ? AND id != ? ORDER BY name");
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
    <link rel="stylesheet" href="../assets/css/materials.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

<main>
     <form method="POST">
            <div class="folder-section">
                <div class="folder-header">
                    <!-- Иконка с выбором -->
                    <div class="folder-icon-edit-wrapper">
                        <div class="folder-icon">
                            <?php if (!empty($folder['icon'])): ?>
                                <img src="../assets/icons/<?php echo htmlspecialchars($folder['icon']); ?>" alt="<?php echo htmlspecialchars($folder['name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="icon-selector-compact">
                            <?php foreach ($availableIcons as $iconFile): ?>
                                <label class="icon-option">
                                    <input type="radio" name="icon" value="<?php echo htmlspecialchars($iconFile); ?>" <?php echo (($folder['icon'] ?? '') === $iconFile) ? 'checked' : ''; ?> required>
                                    <div class="icon-preview">
                                        <img src="../assets/icons/<?php echo htmlspecialchars($iconFile); ?>" alt="<?php echo htmlspecialchars(pathinfo($iconFile, PATHINFO_FILENAME)); ?>">
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Название и теги с кнопками справа -->
                    <div class="folder-info-wrapper-horizontal">
                        <div class="folder-info-wrapper">
                            <input type="text" name="folder_name" value="<?php echo htmlspecialchars($folder['name']); ?>" class="folder-title-edit" required>
                            <input type="text" name="folder_tags" value="<?php echo htmlspecialchars($folder['tags'] ?? ''); ?>" class="tags-input-edit" placeholder="Теги (через пробел, например: #матан #2курс)">
                        </div>
                        
                        <!-- Кнопки управления справа, по центру по вертикали -->
                        <div class="folder-actions">
                            <button type="submit" name="save" class="icon-btn icon-btn-save" title="Сохранить">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <a href="../folders/view.php?id=<?php echo $folder_id; ?>" class="icon-btn icon-btn-cancel" title="Отмена">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Материалы для выбора -->
            <?php if (!empty($materials)): ?>
                <div class="materials-grid" id="materialsGrid">
                    <?php foreach ($materials as $mat): ?>
                        <div class="material-card selectable-material material-card-yellow" 
                             data-material-id="<?php echo $mat['id']; ?>">
                            <h3><?php echo htmlspecialchars($mat['name']); ?></h3>
                            <p>Тип: <?php echo htmlspecialchars($mat['type_name'] ?? 'Не указан'); ?></p>
                            <?php if ($mat['tags']): ?>
                                <p>Теги: <?php echo htmlspecialchars($mat['tags']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Скрытые input для выбранных материалов -->
                <div id="selectedMaterialsInputs"></div>
                
                <!-- Кнопка перемещения -->
                <?php if (!empty($materials)): ?>
                    <div class="buttons buttons-centered">
                        <div class="dropdown move-dropdown">
                            <input type="checkbox" id="move-dropdown" class="dropdown-checkbox">
                            <label for="move-dropdown" class="button_move">
                                Переместить выбранные
                            </label>
                            <ul class="dropdown-menu move-menu">
                                <?php foreach ($other_folders as $f): ?>
                                    <li>
                                        <a href="#" class="move-folder-link" data-folder-id="<?php echo $f['id']; ?>">
                                            <?php echo htmlspecialchars($f['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-message">
                    В этой папке пока нет материалов.
                </div>
            <?php endif; ?>
        </form>


</main>
<script>

    // Обработка выбора иконки - обновление главной иконки
    const iconInputs = document.querySelectorAll('.icon-selector-compact input[type="radio"]');
    const mainIconImg = document.querySelector('.folder-icon img');
    
    iconInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked && mainIconImg) {
                const iconSrc = this.value;
                mainIconImg.src = '../assets/icons/' + iconSrc;
                mainIconImg.alt = iconSrc.replace('.svg', '');
            }
        });
    });

    // Обработка выбора материалов
    const selectedMaterials = new Set();
    const materialsGrid = document.getElementById('materialsGrid');
    const selectedMaterialsInputs = document.getElementById('selectedMaterialsInputs');

    if (materialsGrid) {
        materialsGrid.addEventListener('click', (e) => {
            const materialCard = e.target.closest('.selectable-material');
            if (!materialCard) return;

            const materialId = materialCard.dataset.materialId;
            
            if (selectedMaterials.has(materialId)) {
                selectedMaterials.delete(materialId);
                materialCard.classList.remove('selected');
            } else {
                selectedMaterials.add(materialId);
                materialCard.classList.add('selected');
            }

            updateSelectedInputs();
        });
    }

    function updateSelectedInputs() {
        selectedMaterialsInputs.innerHTML = '';
        selectedMaterials.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_materials[]';
            input.value = id;
            selectedMaterialsInputs.appendChild(input);
        });
    }

    // Обработка перемещения материалов
    const moveFolderLinks = document.querySelectorAll('.move-folder-link');
    moveFolderLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            if (selectedMaterials.size === 0) {
                alert('Выберите материалы для перемещения');
                return;
            }

            const folderId = link.dataset.folderId;
            const form = document.querySelector('form');
            
            // Создаем скрытое поле для целевой папки
            const targetInput = document.createElement('input');
            targetInput.type = 'hidden';
            targetInput.name = 'target_folder';
            targetInput.value = folderId;
            form.appendChild(targetInput);

            // Создаем скрытую кнопку для отправки формы с move
            const moveButton = document.createElement('button');
            moveButton.type = 'submit';
            moveButton.name = 'move';
            moveButton.style.display = 'none';
            form.appendChild(moveButton);

            // Отправляем форму
            moveButton.click();
        });
    });
</script>
</body>
</html>

