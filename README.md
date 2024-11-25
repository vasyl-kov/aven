# **Документація для перевірки проекту: Реєстрація користувачів**

## **1. Опис проекту**

Цей проект реалізує веб-форми для реєстрації користувачів, де користувачі можуть заповнювати свої персональні дані (ім'я, прізвище, email, телефон) та зберігати їх у базі даних SQLite. Після успішної реєстрації користувач буде перенаправлений на сторінку success з персоналізованим повідомленням.

## **2. Використані технології та інструменти**

- **PHP**: серверна частина проекту.
- **SQLite**: використовується для зберігання даних користувачів.
- **HTML, CSS**: для створення веб-форм та лендінгу в цілому.
- **JavaScript**: для валідації введених даних на стороні клієнта.
- **Google Analytics та Facebook Pixel**: для збору аналітики.
- Browser API: для додаткової валідації (required, type).
- WEB API:  для зручної обробки данних з форми.
- **International Telephone Input бібліотека: для роботи з input[type=”tel”].**
- **ipapi.co:  для роботи з геоданними користувача**

## **3. Інструкція по налаштуванню**

1. **Налаштування сервера**:
    - Завантажте проект доступний [***за посиланням***](https://drive.google.com/file/d/13xwJuqYU8SK64sXZVxg9xV9NdULlmaEN/view?usp=drive_link)  на ваш локальний сервер (Apache або Nginx).
    - Переконайтесь, що сервер підтримує PHP.
    - Переконайтесь, що на сервері встановлений SQLite.
2. **Налаштування бази даних**:
    
    Створіть таблицю в базі даних SQLite:
    
    ```sql
    CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    phone TEXT NOT NULL UNIQUE
    );
    ```
    
3. **Налаштування аналітики**:
- Вставте ваші **Facebook Pixel ID** та **Google Analytics ID** замість{fbp}та {ggl} у вашому URL - https://yourhostname/?fbp={fbp}&ggl={ggl}

## **4. Як протестувати проект**

### **Тестування форми реєстрації:**

1. Відкрийте веб-сторінку реєстрації.
2. Заповніть поля: ім'я, прізвище, email, телефон.
3. Перевірте, чи підставляється геоданні користувача в поле телефону, якщо є можливість то змініть ваш IP на інший, розташований в іншій країні
4. Перевірте, чи з'являється повідомлення про успішну реєстрацію.
5. Перевірте, чи відбувається редірект на сторінку **success.php**.
6. Для сторінки **success.php зробив автоматичний переклад під декілька ГЕО (UA, US, FR,  DE та UA як дефолтний).**

### **Тестування на валідність:**

1. Перевірте форму з неповними або неправильними даними (наприклад, порожні поля, дублі email/телефон).
2. Переконайтесь, що система видає відповідні помилки.

### **Тестування логування:**

1. Перевірте, чи записується данні в файл логів logs/form_log.txt.
2. Перевірте, чи містить файл журналу з такими даними:
    - Час запиту.
    - Отримані дані.
    - Відповідь сервера.

## **5. Тестування API**

1. **POST запит на реєстрацію:**
    - Метод: POST
    - URL: http://localhost:8888/aven/submit

Дані:

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890"
}
```

- Очікувана відповідь:

```json
{
    "success": true,
    "redirect_url": "success.php?fbp=testFBP&ggl=testGGL"
}
```

1. Тестування помилок:
    - Запит з порожніми або неправильними даними.
    - Очікувана відповідь:

```json
{
    "success": false,
    "message": "Будь ласка, заповніть всі обов'язкові поля."
}
```

## **6. Логування**

### **Файл журналу: logs/form_log.txt**

- Записи в журналі містять:
    - Час запиту.
    - Дані запиту (POST).
    - Відповідь сервера.

**Приклад запису в файл:**

```php
[2024-11-21 15:45:12] POST /submit
Received Data: {"first_name": "John", "last_name": "Doe", "email": "john.doe@example.com", "phone": "+1234567890"}
Response: {"success": true, "redirect_url": "success.php?fbp=testFBP&ggl=testGGL"}

```

## **7. Звіт про можливі проблеми та їх вирішення**

### **1. Проблема: Запит не обробляється.**

- Перевірте налаштування PHP та серверних прав.
- Переконайтесь, що PHP коректно обробляє запити.

### **2. Проблема: Форма не відправляється.**

- Перевірте правильність HTML та JavaScript коду.
- Переконайтесь, що валідація даних працює.

### **3. Проблема: Логування не працює.**

- Перевірте права доступу до файлу form_log.txt.
- Переконайтесь, що функція запису в файл працює правильно.

## **8. Заключення**

Проект реалізує форму для реєстрації користувачів з валідацією даних та автоматичним логуванням усіх запитів. Використано PHP, SQLite, JavaScript для взаємодії з користувачем і збору даних. Логування запитів допомагає стежити за всіма запитами до сервера.