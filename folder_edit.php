<?php
require 'folder_edit_logic.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($folder['name']); ?> — StudLib</title>
    <link rel="stylesheet" href="folderedit_styles.css">
</head>
<body>
<header>
    <div class="logo">StudLib</div>
    <div class="header_toolbar">
        <div class="prof_pic"></div>
        <div class="nav">
            <a href="folder_look.php">Мои материалы</a>
            <div class="dropdown">
                <input type="checkbox" id="add-dropdown" class="dropdown-checkbox">
                <label for="add-dropdown" class="dropdown-toggle">Добавить</label>
                <ul class="dropdown-menu">
                    <li><a href="#">Добавить папку</a></li>
                    <li><a href="#">Добавить документ</a></li>
                </ul>
            </div>
            <a href="finder.php">Поиск</a>
            <a href="https://web.telegram.org/k/">Перейти в чат</a>
        </div>
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

            <!-- Кнопки управления -->
            <div class="buttons">
                <button type="submit" name="save" class="button_save">Сохранить</button>

                <?php if (!empty($materials)): ?>
                    <button type="submit" name="move" class="button_move">Переместить выбранные</button>
                <?php endif; ?>

                <a href="folder_view.php?id=<?php echo $folder_id; ?>" class="button_cancel">Отмена</a>
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

                            <a href="<?php echo htmlspecialchars($mat['path']); ?>" target="_blank" class="view-link">Просмотреть</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-message">
                    В этой папке пока нет материалов.
                </div>
            <?php endif; ?>
        </form>


</main>
</body>
</html>