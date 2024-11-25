<?php
session_start();

// Перевірка на наявність даних у сесії
if (!isset($_SESSION['user_data'])) {
    header('Location: error.php');
    exit();
}

// Отримуємо дані користувача з сесії
$userData = $_SESSION['user_data'];
$firstName = $userData['first_name'];
$lastName = $userData['last_name'];
$email = $userData['email'];
$phone = $userData['phone'];

// Отримуємо параметр 'lang' з URL
$countryCode = isset($_GET['lang']) ? $_GET['lang'] : 'EN'; // За замовчуванням англійська

// Список перекладів для тексту і заголовка
$translations = [
    'UA' => [
        'title' => "Успішна реєстрація",
        'h1' => "%s, дякую за реєстрацію",
        'message' => "Ви успішно зареєструвались. Очікуйте дзвінок на номер %s або лист на пошту %s.",
    ],
    'US' => [
        'title' => "Successful Registration",
        'h1' => "%s, Thank you for registering",
        'message' => "You have successfully registered. Expect a call on %s or an email at %s.",
    ],
    'FR' => [
        'title' => "Inscription réussie",
        'h1' => "%s, Merci de vous inscrire",
        'message' => "Vous vous êtes inscrit avec succès. Attendez un appel au %s ou un e-mail à %s.",
    ],
    'DE' => [
        'title' => "Erfolgreiche Registrierung",
        'h1' => "%s, Vielen Dank für Ihre Anmeldung",
        'message' => "Sie haben sich erfolgreich registriert. Erwarten Sie einen Anruf am %s oder eine E-Mail an %s.",
    ],
    'default' => [
        'title' => "Successful Registration",
        'h1' => "%s, Thank you for registering",
        'message' => "You have successfully registered. Expect a call on %s or an email at %s.",
    ],
];

// Вибір перекладу
$translation = $translations[$countryCode] ?? $translations['default'];
$title = $translation['title'];
$h1 = $translation['h1'];
$message = $translation['message'];

?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($countryCode); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body {
            min-height: 320px;
        }
        .success__container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border: 1px solid black;
            border-radius: 12px;
            width: fit-content;
            padding: 10px 20px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="success__container">
        <h1><?php printf($h1, htmlspecialchars($firstName)); ?></h1>
        <p><?php printf($message, '<b>' . htmlspecialchars($phone) . '</b>', '<b>' . htmlspecialchars($email) . '</b>'); ?></p>
    </div>
</body>
</html>
