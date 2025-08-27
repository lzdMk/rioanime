/**
 * Authentication JavaScript
 * Handles login and registration functionalities
 */
document.addEventListener('DOMContentLoaded', function() {
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const errorAlert = document.getElementById('loginFormErrors');
            if (errorAlert) errorAlert.classList.add('d-none');

            const formData = new FormData(loginForm);
            // Add CSRF token if present
            const csrfInputs = loginForm.querySelectorAll('input[type="hidden"]');
            csrfInputs.forEach(input => {
                formData.set(input.name, input.value);
            });

            const baseUrl = window.baseUrl || '/rioanime/';
            const url = baseUrl.endsWith('/') ? baseUrl + 'account/login' : baseUrl + '/account/login';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessNotification(data.message);
                    setTimeout(() => {
                        const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                        if (loginModal) loginModal.hide();
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    if (errorAlert) {
                        errorAlert.textContent = data.message;
                        errorAlert.classList.remove('d-none', 'alert-success');
                        errorAlert.classList.add('alert-danger');
                    }
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                if (errorAlert) {
                    errorAlert.textContent = 'An error occurred. Please try again.';
                    errorAlert.classList.remove('d-none');
                }
            });
        });
    }
    // Logout button handler
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const baseUrl = window.baseUrl || '/rioanime/';
            const url = baseUrl.endsWith('/') ? baseUrl + 'account/logout' : baseUrl + '/account/logout';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || baseUrl;
                } else {
                    console.log('Logout failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
            });
        });
    }
    // Registration form handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset any previous errors
            resetFormErrors(registerForm);
            const errorAlert = document.getElementById('registerFormErrors');
            errorAlert.classList.add('d-none');
            
            // Get form data
            const formData = new FormData(registerForm);
            // Ensure CSRF token is always appended (regardless of its name)
            const csrfInputs = registerForm.querySelectorAll('input[type="hidden"]');
            csrfInputs.forEach(input => {
                formData.set(input.name, input.value);
            });
            
            // Validate password match
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            if (password !== confirmPassword) {
                showFormError('registerConfirmPassword', 'Passwords do not match');
                return;
            }
            
            // Validate terms agreement
            const agreeTerms = document.getElementById('agreeTerms');
            if (!agreeTerms.checked) {
                const errorAlert = document.getElementById('registerFormErrors');
                errorAlert.textContent = 'You must agree to the Terms of Service and Privacy Policy to continue.';
                errorAlert.classList.remove('d-none', 'alert-success');
                errorAlert.classList.add('alert-danger');
                agreeTerms.focus();
                return;
            }
            
            // Submit form via AJAX
            const baseUrl = window.baseUrl || '/rioanime/';
            // Ensure proper URL construction
            const url = baseUrl.endsWith('/') ? baseUrl + 'account/register' : baseUrl + '/account/register';
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message inside registration modal
                    showRegisterSuccessNotification(data.message);
                    
                    // Check if auto-login was successful
                    if (data.auto_login) {
                        setTimeout(() => {
                            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                            if (registerModal) {
                                registerModal.hide();
                            }
                            registerForm.reset();
                            
                            // Refresh the page to show logged-in state
                            window.location.href = data.redirect_url || window.location.href;
                        }, 1500);
                    } else {
                        // Fallback if auto-login didn't work
                        setTimeout(() => {
                            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                            if (registerModal) {
                                registerModal.hide();
                            }
                            registerForm.reset();
                        }, 1500);
                    }
                } else {
                    // Show errors
                    console.log('Registration failed:', data);
                    if (data.errors) {
                        console.log('Validation errors:', data.errors);
                        for (const field in data.errors) {
                            showFormError(getFieldId(field), data.errors[field]);
                        }
                    }
                    // Show general error message
                    if (data.message) {
                        errorAlert.textContent = data.message;
                        errorAlert.classList.remove('d-none', 'alert-success');
                        errorAlert.classList.add('alert-danger');
                    }
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                errorAlert.textContent = 'An error occurred. Please try again.';
                errorAlert.classList.remove('d-none');
            });
        });
    }
    
    // Reset registration success message when modal is opened
    const registerModalEl = document.getElementById('registerModal');
    if (registerModalEl) {
        registerModalEl.addEventListener('show.bs.modal', function() {
            const errorAlert = document.getElementById('registerFormErrors');
            if (errorAlert) {
                errorAlert.textContent = '';
                errorAlert.classList.add('d-none');
                errorAlert.classList.remove('alert-success', 'alert-danger');
            }
        });
    }
    
    // Live validation for username
    const usernameInput = document.getElementById('registerUsername');
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                checkAvailability('username', this.value);
            }
        });
    }
    
    // Live validation for email
    const emailInput = document.getElementById('registerEmail');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                checkAvailability('email', this.value);
            }
        });
    }
});

/**
 * Check if username or email is available
 */
function checkAvailability(type, value) {
    const endpoint = type === 'username' ? 'check-username' : 'check-email';
    const fieldId = type === 'username' ? 'registerUsername' : 'registerEmail';
    
    const formData = new FormData();
    formData.append(type, value);
    
    const baseUrl = window.baseUrl || '/rioanime/';
    const url = baseUrl.endsWith('/') ? baseUrl + 'account/' + endpoint : baseUrl + '/account/' + endpoint;
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.available) {
            showFormError(fieldId, type.charAt(0).toUpperCase() + type.slice(1) + ' is already taken');
        }
    })
    .catch(error => {
        console.error('Availability check error:', error);
    });
}

/**
 * Show form validation error
 */
function showFormError(fieldId, errorMessage) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.add('is-invalid');
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = errorMessage;
        }
    }
}

/**
 * Reset all form errors
 */
function resetFormErrors(form) {
    const fields = form.querySelectorAll('.is-invalid');
    fields.forEach(field => {
        field.classList.remove('is-invalid');
    });
}

/**
 * Get the form field ID from the field name
 */
function getFieldId(fieldName) {
    // Convert snake_case to camelCase and prepend 'register'
    const camelCase = fieldName.replace(/_([a-z])/g, function(g) { return g[1].toUpperCase(); });
    return 'register' + camelCase.charAt(0).toUpperCase() + camelCase.slice(1);
}

/**
 * Modal switching functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Handle switching from register to login
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('login-link') || e.target.closest('.login-link')) {
            e.preventDefault();
            
            // Hide register modal
            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
            if (registerModal) {
                registerModal.hide();
            }
            
            // Show login modal after a short delay
            setTimeout(() => {
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            }, 300);
        }
        
        // Handle switching from login to register
        if (e.target.classList.contains('register-link') || e.target.closest('.register-link')) {
            e.preventDefault();
            
            // Hide login modal
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (loginModal) {
                loginModal.hide();
            }
            
            // Show register modal after a short delay
            setTimeout(() => {
                const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
                registerModal.show();
            }, 300);
        }
    });
});

/**
 * Show success notification
 */
function showSuccessNotification(message) {
    // Show green alert inside login modal
    const errorAlert = document.getElementById('loginFormErrors');
    if (errorAlert) {
        errorAlert.textContent = message;
        errorAlert.classList.remove('d-none', 'alert-danger');
        errorAlert.classList.add('alert-success');
    } else {
        alert(message); // fallback
    }
}

/**
 * Show registration success notification
 */
function showRegisterSuccessNotification(message) {
    // Show green alert inside registration modal
    const errorAlert = document.getElementById('registerFormErrors');
    if (errorAlert) {
        errorAlert.textContent = message;
        errorAlert.classList.remove('d-none', 'alert-danger');
        errorAlert.classList.add('alert-success');
    } else {
        alert(message); // fallback
    }
}
