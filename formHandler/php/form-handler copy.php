<?php
// Встановлюємо заголовок для відповіді як JSON
header('Content-Type: application/json');

// Перевіряємо, чи були отримані дані з форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Збираємо дані з форми
    $firstName = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $lastName = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

    // Перевірка, чи всі необхідні поля заповнені
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
        // Якщо якісь обов'язкові поля порожні, повертаємо помилку
        echo json_encode([
            'success' => false,
            'message' => 'Будь ласка, заповніть всі обов\'язкові поля.'
        ]);
        exit();
    }

    // Підключення до бази даних SQLite
    $dbPath = dirname(__DIR__) . '/database/database.sqlite'; // Шлях до вашої бази даних
    try {
        // Підключення до бази даних
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Видалення таблиці, якщо вона існує
        $pdo->exec("DROP TABLE IF EXISTS users");

        // Створення нової таблиці з правильними стовпцями
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL
            );
        ";
        $pdo->exec($createTableQuery);

        // Вставка даних у таблицю
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $phone]);

        // Повертаємо успішну відповідь
        echo json_encode([
            'success' => true,
            'message' => 'Дані успішно збережено.',
            'redirect_url' => 'https://example.com/success'  // Замість цього URL поставте той, на який потрібно перенаправити
        ]);
    } catch (PDOException $e) {
        // Якщо виникла помилка при роботі з базою даних, повертаємо помилку
        echo json_encode([
            'success' => false,
            'message' => 'Помилка при збереженні даних: ' . $e->getMessage()
        ]);
    }
} else {
    // Якщо запит не є POST
    echo json_encode([
        'success' => false,
        'message' => 'Невірний метод запиту.'
    ]);
}

exit(); // Завершуємо виконання скрипта
