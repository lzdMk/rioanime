/**
 * Authentication JavaScript
 * Handles login and registration functionalities
 */
document.addEventListener('DOMContentLoaded', function() {
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
            
            // Validate password match
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            if (password !== confirmPassword) {
                showFormError('registerConfirmPassword', 'Passwords do not match');
                return;
            }
            
            // Submit form via AJAX
            fetch(window.baseUrl + 'account/register', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and close modal
                    showSuccessNotification(data.message);
                    
                    // Close modal after a slight delay
                    setTimeout(() => {
                        const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                        if (registerModal) {
                            registerModal.hide();
                        }
                        
                        // Clear form
                        registerForm.reset();
                    }, 1500);
                } else {
                    // Show errors
                    if (data.errors) {
                        for (const field in data.errors) {
                            showFormError(getFieldId(field), data.errors[field]);
                        }
                    }
                    
                    // Show general error message
                    if (data.message) {
                        errorAlert.textContent = data.message;
                        errorAlert.classList.remove('d-none');
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
    
    fetch(window.baseUrl + 'account/' + endpoint, {
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
 * Show success notification
 */
function showSuccessNotification(message) {
    // You can implement a toast notification here
    alert(message); // Simple alert for now
}
