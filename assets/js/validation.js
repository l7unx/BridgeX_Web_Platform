// assets/js/validation.js
// BridgeX Platform — Client-side Form Validation

document.addEventListener('DOMContentLoaded', function () {

    // ── Contact Form ──
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            var valid = true;

            var name = document.getElementById('name');
            var email = document.getElementById('email');
            var message = document.getElementById('message');

            clearErrors();

            if (!name.value.trim()) {
                showError('name-error', 'Full name is required.');
                valid = false;
            } else if (name.value.trim().length < 2) {
                showError('name-error', 'Name must be at least 2 characters.');
                valid = false;
            }

            if (!email.value.trim()) {
                showError('email-error', 'Email address is required.');
                valid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError('email-error', 'Please enter a valid email address.');
                valid = false;
            }

            if (!message.value.trim()) {
                showError('message-error', 'Message is required.');
                valid = false;
            } else if (message.value.trim().length < 10) {
                showError('message-error', 'Message must be at least 10 characters.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ── Offer Form ──
    var offerForm = document.getElementById('offerForm');
    if (offerForm) {
        offerForm.addEventListener('submit', function (e) {
            var valid = true;

            var price    = document.getElementById('price');
            var delivery = document.getElementById('delivery_time');
            var message  = document.getElementById('message');

            clearErrors();

            if (!price.value.trim()) {
                showError('price-error', 'Price is required.');
                valid = false;
            } else if (isNaN(price.value) || Number(price.value) <= 0) {
                showError('price-error', 'Please enter a valid positive price.');
                valid = false;
            }

            if (!delivery.value.trim()) {
                showError('delivery-error', 'Delivery time is required.');
                valid = false;
            } else if (delivery.value.trim().length < 2) {
                showError('delivery-error', 'Please provide a valid delivery time.');
                valid = false;
            }

            if (!message.value.trim()) {
                showError('message-error', 'Cover message is required.');
                valid = false;
            } else if (message.value.trim().length < 20) {
                showError('message-error', 'Message must be at least 20 characters.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ── Helpers ──
    function showError(id, msg) {
        var el = document.getElementById(id);
        if (el) el.textContent = msg;
    }

    function clearErrors() {
        var errors = document.querySelectorAll('.field-error');
        errors.forEach(function (el) { el.textContent = ''; });
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // ── Live validation on blur ──
    var inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(function (input) {
        input.addEventListener('blur', function () {
            if (!this.value.trim()) {
                this.style.borderColor = 'rgba(255,107,107,0.6)';
            } else {
                this.style.borderColor = 'rgba(215,101,154,0.6)';
            }
        });
        input.addEventListener('focus', function () {
            this.style.borderColor = 'rgba(215,101,154,0.6)';
        });
    });
});
