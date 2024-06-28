document.addEventListener('DOMContentLoaded', function() {
    // Client-side validation for registration form
    var registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            var email = document.getElementById('email').value;
            var regNumber = document.getElementById('registration_number').value;

            if (!/^[a-zA-Z0-9._%+-]+@ptuniv\.edu\.in$/.test(email)) {
                alert('Invalid email address. Must be a ptuniv.edu.in email.');
                event.preventDefault();
            }

            if (!/^\d{2}[A-Za-z]{3}\d{4}$/.test(regNumber)) {
                alert('Invalid registration number. Format: 2 digits, 3 letters, 4 digits.');
                event.preventDefault();
            }
        });
    }

    // Client-side validation for login form
    var loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            var email = document.getElementById('email').value;
            var regNumber = document.getElementById('registration_number').value;

            if (!/^[a-zA-Z0-9._%+-]+@ptuniv\.edu\.in$/.test(email)) {
                alert('Invalid email address. Must be a ptuniv.edu.in email.');
                event.preventDefault();
            }

            if (!/^\d{2}[A-Za-z]{3}\d{4}$/.test(regNumber)) {
                alert('Invalid registration number. Format: 2 digits, 3 letters, 4 digits.');
                event.preventDefault();
            }
        });
    }
});
