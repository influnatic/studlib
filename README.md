# StudLib - Быстрый старт

## macOS/Linux

1. Запустите MySQL:
```bash
brew services start mysql
```

2. Создайте базу данных:
```bash
mysql -u root -e "CREATE DATABASE studlib CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

3. Наполните базу данных:
```bash
mysql -u root studlib < sql/init_database.sql
```

4. Запустите сервер:
```bash
php -S localhost:8000
```

5. Откройте: `http://localhost:8000`

## Windows

1. Запустите MySQL (через XAMPP или отдельно)

2. Создайте базу данных:
```cmd
mysql -u root -e "CREATE DATABASE studlib CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

3. Наполните базу данных:
```cmd
mysql -u root studlib < sql/init_database.sql
```

4. Запустите сервер:
```cmd
php -S localhost:8000
```

5. Откройте: `http://localhost:8000`

## Настройка БД

Если нужен пароль для MySQL, отредактируйте `config/db.php`

