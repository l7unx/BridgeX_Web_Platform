// Slider
var currentSlide = 0;

function showSlide(index) {
    var slides = document.querySelectorAll('.image-slider .slider-photo');

    if (slides.length === 0) return;

    slides[currentSlide].classList.remove('active');

    currentSlide = index;

    if (currentSlide < 0) {
        currentSlide = slides.length - 1;
    }

    if (currentSlide >= slides.length) {
        currentSlide = 0;
    }

    slides[currentSlide].classList.add('active');
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
}

setInterval(function () {
    showSlide(currentSlide + 1);
}, 5000);


// Register
var registerForm = document.getElementById('registerForm');

if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        var valid = true;
        var errors = document.querySelectorAll('.field-error');

        for (var i = 0; i < errors.length; i++) {
            errors[i].textContent = '';
        }

        var name     = document.getElementById('name').value.trim();
        var email    = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;
        var confirm  = document.getElementById('confirm').value;
        var role     = document.getElementById('role').value;

        if (name.length < 3) {
            document.getElementById('nameError').textContent = 'Name must be at least 3 characters.';
            valid = false;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            document.getElementById('emailError').textContent = 'Invalid email format.';
            valid = false;
        }

        if (password.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters.';
            valid = false;
        }

        if (password !== confirm) {
            document.getElementById('confirmError').textContent = 'Passwords do not match.';
            valid = false;
        }

        if (!role) {
            document.getElementById('roleError').textContent = 'Please select an account type.';
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}


// Login
var loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        var valid = true;
        var errors = document.querySelectorAll('.field-error');

        for (var i = 0; i < errors.length; i++) {
            errors[i].textContent = '';
        }

        var email    = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            document.getElementById('emailError').textContent = 'Invalid email format.';
            valid = false;
        }

        if (password.length < 1) {
            document.getElementById('passwordError').textContent = 'Please enter your password.';
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}


// Post Project Form
let currentStep = 1;
const totalSteps = 3;

// Next Step
function nextStep(step) {
    if (!validateStep(step)) return;

    var currentProgress = document.querySelector('[data-step="' + step + '"]');
    if (currentProgress) {
        currentProgress.classList.add('done');
        currentProgress.classList.remove('active');
    }

    var currentBox = document.getElementById('step-' + step);
    if (currentBox) {
        currentBox.classList.add('hidden');
    }

    currentStep = step + 1;

    var nextBox = document.getElementById('step-' + currentStep);
    if (nextBox) {
        nextBox.classList.remove('hidden');
    }

    var nextEl = document.querySelector('[data-step="' + currentStep + '"]');
    if (nextEl) {
        nextEl.classList.add('active');
    }

    if (currentStep === 3) {
        buildSummary();
    }

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Previous Step
function prevStep(step) {
    var currentBox = document.getElementById('step-' + step);
    if (currentBox) {
        currentBox.classList.add('hidden');
    }

    currentStep = step - 1;

    var previousBox = document.getElementById('step-' + currentStep);
    if (previousBox) {
        previousBox.classList.remove('hidden');
    }

    var previousProgress = document.querySelector('[data-step="' + currentStep + '"]');
    if (previousProgress) {
        previousProgress.classList.add('active');
        previousProgress.classList.remove('done');
    }

    var currentProgress = document.querySelector('[data-step="' + step + '"]');
    if (currentProgress) {
        currentProgress.classList.remove('active');
    }

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Validate Step
function validateStep(step) {
    let valid = true;

    if (step === 1) {
        valid = checkField('title', 'Please enter the project title')
            & checkField('category', 'Please select a project category')
            & checkTextarea('description', 'Please enter a project description', 20);
    }

    if (step === 2) {
        valid = checkField('budget', 'Please enter the estimated budget')
            & checkField('duration', 'Please select the expected duration');

        var budgetInput = document.getElementById('budget');

        if (budgetInput) {
            var budgetVal = budgetInput.value.trim();

            if (budgetVal && isNaN(budgetVal)) {
                showError('err-budget', 'Please enter a valid number');
                valid = false;
            }
        }
    }

    return !!valid;
}

function checkField(id, msg) {
    var el = document.getElementById(id);
    var err = document.getElementById('err-' + id);

    if (!el) return true;

    if (!el.value.trim()) {
        if (err) {
            showError('err-' + id, msg);
        }

        el.focus();
        return false;
    }

    if (err) {
        clearError('err-' + id);
    }

    return true;
}

function checkTextarea(id, msg, minLen) {
    var el = document.getElementById(id);
    var err = document.getElementById('err-' + id);

    if (!el) return true;

    if (el.value.trim().length < minLen) {
        if (err) {
            showError('err-' + id, msg + ' (' + minLen + ' characters minimum)');
        }

        el.focus();
        return false;
    }

    if (err) {
        clearError('err-' + id);
    }

    return true;
}

function showError(id, msg) {
    var el = document.getElementById(id);

    if (el) {
        el.textContent = msg;
    }
}

function clearError(id) {
    var el = document.getElementById(id);

    if (el) {
        el.textContent = '';
    }
}

// Build Summary
function buildSummary() {
    var container = document.getElementById('project-summary');

    if (!container) return;

    var categoryLabels = {
        web: 'Website',
        mobile: 'Mobile App',
        design: 'UI/UX Design',
        backend: 'Backend / API',
        ecommerce: 'E-commerce Store',
        other: 'Other'
    };

    var durationLabels = {
        less_week: 'Less than a week',
        '1_2_weeks': '1-2 weeks',
        '1_month': '1 month',
        '2_3_months': '2-3 months',
        more_3: 'More than 3 months'
    };

    var title = document.getElementById('title').value;
    var category = document.getElementById('category').value;
    var desc = document.getElementById('description').value;
    var budget = document.getElementById('budget').value;
    var duration = document.getElementById('duration').value;
    var skills = document.getElementById('skills').value;
    var notes = document.getElementById('notes').value;

    var items = [
        {
            label: 'Project Title',
            value: title,
            full: false
        },
        {
            label: 'Category',
            value: categoryLabels[category] || category,
            full: false
        },
        {
            label: 'Budget',
            value: budget + ' SAR',
            full: false
        },
        {
            label: 'Expected Duration',
            value: durationLabels[duration] || duration,
            full: false
        },
        {
            label: 'Required Skills',
            value: skills || '—',
            full: false
        },
        {
            label: 'Project Description',
            value: desc,
            full: true
        },
        {
            label: 'Additional Notes',
            value: notes || '—',
            full: true
        }
    ];

    container.innerHTML = items.map(function (item) {
        return `
            <div class="summary-item ${item.full ? 'full-width' : ''}">
                <label>${item.label}</label>
                <span>${escapeHtml(item.value)}</span>
            </div>
        `;
    }).join('');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// Live clear errors on input
document.addEventListener('DOMContentLoaded', function () {
    ['title', 'category', 'description', 'budget', 'duration'].forEach(function (id) {
        var el = document.getElementById(id);

        if (el) {
            el.addEventListener('input', function () {
                clearError('err-' + id);
            });

            el.addEventListener('change', function () {
                clearError('err-' + id);
            });
        }
    });
});


// Validation
document.addEventListener('DOMContentLoaded', function () {

    // Contact Form
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

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // Offer Form
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

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // Helpers
    function clearErrors() {
        var errors = document.querySelectorAll('.field-error');
        errors.forEach(function (el) {
            el.textContent = '';
        });
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Live validation on blur
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