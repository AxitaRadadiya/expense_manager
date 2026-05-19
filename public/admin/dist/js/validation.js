function Validation(formId) {

    var form = document.getElementById(formId);
    if (!form) return;

    var mobile = form.querySelector('#mobile');
    var email = form.querySelector('#email');

    var mobilePattern = /^\d{10}$/;
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function setError(input, msg) {
        var group = input.closest('.form-group');
        var error = group.querySelector('[data-error-for="' + input.id + '"]');

        if (!error) {
            error = document.createElement('div');
            error.className = 'invalid-feedback d-block';
            error.setAttribute('data-error-for', input.id);
            input.after(error);
        }

        input.classList.add('is-invalid');
        error.textContent = msg;
    }

    function clearError(input) {
        var group = input.closest('.form-group');
        var error = group.querySelector('[data-error-for="' + input.id + '"]');

        input.classList.remove('is-invalid');
        if (error) error.textContent = '';
    }

    function validateMobile() {
        if (!mobile) return true;
        var v = mobile.value.trim();
        if (!v) { setError(mobile, 'Mobile is required'); return false; }
        //if (!mobilePattern.test(v)) { setError(mobile, 'Enter 10 digits'); return false; }
                if (!mobilePattern.test(v)) { setError(mobile, ''); return false; }

        clearError(mobile);
        return true;
    }

    function validateEmail() {
        if (!email) return true;
        var v = email.value.trim();
        if (!v) { setError(email, 'Email is required'); return false; }
        
        //if (!emailPattern.test(v)) { setError(email, 'Invalid email'); return false; }
        if (!emailPattern.test(v)) { setError(email, ''); return false; }

        clearError(email);
        return true;
    }

    if (mobile) {
        mobile.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            validateMobile();
        });
    }

    if (email) {
        email.addEventListener('input', validateEmail);
    }

    form.addEventListener('submit', function (e) {
        if (!(validateMobile() && validateEmail())) {
            e.preventDefault();
        }
    });
}

// global wrapper expected by blade templates
window.bindValidation = function(formId) {
    try {
        Validation(formId);
    } catch (err) {
        console.error('bindValidation error', err);
    }
};