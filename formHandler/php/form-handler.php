<?php
// Функція для логування запитів та відповідей
function logRequest($requestData, $responseData) {
    // Відкриваємо файл журналу для запису
    $logFile = dirname(__DIR__) . '/logs/form_log.txt';
    
    // Формуємо інформацію для запису в журнал
    $logEntry = [
        'time' => date('Y-m-d H:i:s'), // Поточний час
        'request_method' => $_SERVER['REQUEST_METHOD'], // Метод запиту (GET, POST і т.д.)
        'request_url' => $_SERVER['REQUEST_URI'], // URL запиту
        'request_data' => json_encode($requestData), // Дані запиту (JSON)
        'response_data' => json_encode($responseData), // Дані відповіді (JSON)
    ];
    
    // Формуємо строку для запису в файл
    $logString = implode(' | ', $logEntry) . PHP_EOL;
    
    // Записуємо в файл
    file_put_contents($logFile, $logString, FILE_APPEND);
}

// Встановлюємо заголовок для відповіді як JSON
header('Content-Type: application/json');
session_start();

// Перевіряємо, чи були отримані дані з форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Збираємо дані з форми
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    // Перевірка, чи всі необхідні поля заповнені
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
        $response = [
            'success' => false,
            'message' => 'Будь ласка, заповніть всі обов\'язкові поля.'
        ];
        logRequest($_POST, $response); // Логуємо запит і відповідь
        echo json_encode($response);
        exit();
    }

    // Підключення до бази даних SQLite
    $dbPath = dirname(__DIR__) . '/database/database.sqlite';
    try {
        // Підключення до бази даних
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Перевірка на унікальність email і phone
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$email, $phone]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $response = [
                'success' => false,
                'message' => 'Користувач з такою поштою або номером телефону вже існує.'
            ];
            logRequest($_POST, $response); // Логуємо запит і відповідь
            echo json_encode($response);
            exit();
        }

        // Вставка даних у таблицю
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $phone]);

        // Збереження даних у сесію для success.php
        $_SESSION['user_data'] = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone
        ];

        // Підготовка URL для редіректу з GET-параметрами fbp та ggl
        $redirectUrl = 'success.php';

        // Додаємо UTM-мітки до URL (якщо вони є)
        $queryParams = [];
        if (isset($_GET['fbp'])) {
            $queryParams['fbp'] = htmlspecialchars($_GET['fbp']);
        }
        if (isset($_GET['ggl'])) {
            $queryParams['ggl'] = htmlspecialchars($_GET['ggl']);
        }

        // Якщо є GET параметри, додаємо їх до редіректу
        if (!empty($queryParams)) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }

        // Відправка успішної відповіді з редіректом
        $response = [
            'success' => true,
            'redirect_url' => $redirectUrl
        ];
        logRequest($_POST, $response); // Логуємо запит і відповідь
        echo json_encode($response);
    } catch (PDOException $e) {
        // Якщо виникла помилка при роботі з базою даних
        $response = [
            'success' => false,
            'message' => 'Помилка при збереженні даних: ' . $e->getMessage()
        ];
        logRequest($_POST, $response); // Логуємо запит і відповідь
        echo json_encode($response);
    }
} else {
    // Якщо запит не є POST
    $response = [
        'success' => false,
        'message' => 'Невірний метод запиту.'
    ];
    logRequest($_POST, $response); // Логуємо запит і відповідь
    echo json_encode($response);
}
exit();
