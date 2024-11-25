document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.leadform');
    const modal = document.getElementById('modal');
    const modalMessage = document.getElementById('modal-message');

    // Функція для отримання коду країни
    function getCountryCode() {
        return new Promise((resolve, reject) => {
            fetch('https://ipapi.co/json/')
                .then((response) => {
                    if (!response.ok) throw new Error('Не вдалося отримати дані країни.');
                    return response.json();
                })
                .then((data) => resolve(data.country_code))
                .catch(() => resolve('UA')); // За замовчуванням — Україна
        });
    }

    // Підключення intl-tel-input
    forms.forEach(form => {
        const phoneInput = form.querySelector('input[type="tel"]');
        getCountryCode().then((countryCode) => {
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: countryCode,
                utilsScript: 'js/utils.js'
            });
            localStorage.setItem('countryCode', countryCode);
        });

        form.addEventListener('submit', async event => {
            event.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            const countryCode = localStorage.getItem('countryCode') || 'UA';
            formData.append('country_code', countryCode);

            try {
                const response = await fetch('./formHandler/php/form-handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json(); // Читаємо JSON відповідь

                console.log('Server response:', result); // Виводимо відповідь від сервера в консоль для дебагу

                if (result.success) {
                    window.location.href = `${result.redirect_url}?fbp=${fbp}&ggl=${ggl}&lang=${localStorage.getItem('countryCode')}`;
                } else {
                    alert(result.message || 'Помилка при відправці даних. Спробуйте ще раз.');
                }
            } catch (error) {
                console.error('Error:', error); // Логуємо помилку
                alert('Помилка при відправці даних. Спробуйте ще раз.');
            }
        });
    });
});

// Функція для отримання значення параметра з URL
function getURLParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Отримуємо значення для fbp та ggl
const fbp = getURLParameter('fbp');
const ggl = getURLParameter('ggl');
console.log(fbp, ggl);

// Якщо значення є, записуємо їх у приховані поля форми
if (fbp) {
    document.querySelector('input[name="fbp"]').value = fbp;
}

if (ggl) {
    document.querySelector('input[name="ggl"]').value = ggl;
}