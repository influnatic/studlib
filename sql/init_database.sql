-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы типов материалов
CREATE TABLE IF NOT EXISTS mat_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы папок
CREATE TABLE IF NOT EXISTS folders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    tags TEXT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы материалов
CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type_id INT NOT NULL,
    folder_id INT DEFAULT NULL,
    user_id INT NOT NULL,
    path VARCHAR(255) DEFAULT NULL,
    tags TEXT DEFAULT NULL,
    content TEXT DEFAULT NULL,
    FOREIGN KEY (type_id) REFERENCES mat_types(id) ON DELETE CASCADE,
    FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Типы материалов
INSERT INTO mat_types (name) VALUES
    ('Конспект'),
    ('Презентация'),
    ('Таблица'),
    ('Изображение'),
    ('Текстовый файл');

-- Пользователи
INSERT INTO users (username, password, display_name) VALUES
    ('ivanivanov', 'password', 'Иван Иванов'),
    ('mariapetrova', 'password', 'Мария Петрова'),
    ('alexsidorov', 'password', 'Алексей Сидоров');

-- Папки
INSERT INTO folders (name, user_id, tags) VALUES
    ('Математика', 1, '#математика'),
    ('Физика', 1, '#физика'),
    ('Русский язык', 2, '#русский_язык'),
    ('Литература', 2, '#литература'),
    ('Программирование', 3, '#программирование');

-- Материалы
INSERT INTO materials (name, type_id, content, folder_id, user_id, path, tags) VALUES
    ('Производные функций', 1,
     'Производная функции f(x) — это мера изменения функции относительно изменения переменной x.
     Обозначается f\'(x) или df/dx.
     Основные правила:
     1. Производная суммы: (f+g)\' = f\' + g\'
     2. Производная произведения: (fg)\' = f\'g + fg\'
     3. Производная частного: (f/g)\' = (f\'g - fg\')/g^2
     4. Производная сложной функции (цепное правило): (f(g(x)))\' = f\'(g(x)) * g\'(x)
     Примеры:
     - f(x) = x^2 ⇒ f\'(x) = 2x
     - f(x) = sin(x) ⇒ f\'(x) = cos(x)
     - f(x) = e^x ⇒ f\'(x) = e^x
     Применение: нахождение скорости изменения, касательных к кривой, экстремумов функции.',
     1, 1, 'uploads/1/derivatives.pdf', '#математика #производные'),

    ('Интегралы', 1,
     'Интеграл функции f(x) на интервале [a,b] представляет собой площадь под графиком функции.
     Основные виды интегралов:
     1. Неопределенный интеграл: ∫f(x)dx — семейство первообразных функции.
     2. Определенный интеграл: ∫_a^b f(x)dx — число, площадь под графиком между a и b.
     Правила интегрирования:
     - ∫x^n dx = x^(n+1)/(n+1) + C, n ≠ -1
     - ∫e^x dx = e^x + C
     - ∫sin(x) dx = -cos(x) + C
     - ∫cos(x) dx = sin(x) + C
     Методы:
     - Подстановка
     - Интегрирование по частям
     Примеры:
     ∫(2x)dx = x^2 + C
     ∫x*e^x dx = e^x(x-1) + C',
     1, 1, 'uploads/1/integrals.pdf', '#математика #интегралы'),

    ('Законы Ньютона', 1,
     'Законы Ньютона описывают движение тел и взаимодействие сил:
     1. Первый закон (закон инерции): тело сохраняет состояние покоя или равномерного прямолинейного движения, если на него не действуют внешние силы.
     2. Второй закон: F = ma, сила равна произведению массы на ускорение.
     3. Третий закон: действие равно противодействию — сила, с которой тело А действует на тело Б, равна по величине и противоположна по направлению силе, с которой Б действует на А.
     Примеры применения:
     - Подъем груза с помощью блока
     - Движение автомобиля
     - Полет ракеты',
     2, 1, 'uploads/1/newton_laws.pdf', '#физика #законы'),

    ('Синтаксис Python', 1,
     'Основы синтаксиса языка Python:
     1. Объявление переменных: x = 10, name = "Ivan"
     2. Условные конструкции: if, elif, else
     3. Циклы: for, while
     4. Функции: def my_function(params): ...
     5. Работа с файлами: open("file.txt", "r"), read(), write()
     6. Списки, словари, множества
     Примеры:
     def factorial(n):
         if n == 0:
             return 1
         else:
             return n * factorial(n-1)
     Применение: автоматизация, анализ данных, веб-разработка, скрипты.',
     5, 3, 'uploads/3/python_syntax.pdf', '#программирование #python'),

    ('Алгебраические выражения', 1,
     'Правила работы с алгебраическими выражениями:
     1. Раскрытие скобок: a(b+c) = ab + ac
     2. Приведение подобных членов: 2x + 3x = 5x
     3. Упрощение дробей: (2x^2)/(4x) = x/2
     4. Решение уравнений: ax + b = 0 ⇒ x = -b/a
     Примеры:
     - (x+1)^2 = x^2 + 2x + 1
     - (2x-3)(x+5) = 2x^2 + 7x - 15
     Применение: алгебраические преобразования, упрощение формул, подготовка к экзаменам',
     1, 1, 'uploads/1/algebra.pdf', '#математика #алгебра'),

    ('Правописание частей речи', 1,
     'Русский язык: правописание частей речи
     1. Существительные: пишутся с заглавной буквы в начале предложения, склоняются по падежам.
     2. Глаголы: имеют времена (настоящее, прошедшее, будущее), залоги (действительный, страдательный).
     3. Прилагательные: согласуются с существительными в роде, числе и падеже.
     4. Наречия: неизменяемые, отвечают на вопросы где? как? когда?
     5. Правописание: правила орфографии и пунктуации, написание с дефисом, слитно или раздельно.
     Примеры:
     - Он пошёл в школу.
     - Красивый день.
     - Очень быстро бегает.',
     3, 2, 'uploads/2/russian_grammar.pdf', '#русский_язык #грамматика'),

    ('Основы литературы', 1,
     'Литература — это изучение художественных текстов.
     1. Жанры: роман, рассказ, поэзия, драма.
     2. Сюжет: последовательность событий, конфликт, кульминация.
     3. Персонажи: главные, второстепенные, протагонисты, антагонисты.
     4. Темы: любовь, дружба, борьба, мораль.
     Примеры анализа:
     - Роман "Война и мир" — историческая драма о судьбах людей.
     - Рассказ "Шинель" — изучение образа маленького человека.',
     4, 2, 'uploads/2/literature_basics.pdf', '#литература #жанры');
