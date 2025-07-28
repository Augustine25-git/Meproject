/**
 * Sprint 6 - JavaScript Form Validation
 * Validates email address structure, date range, and human name with acceptable characters
 */

// Email validation function
function validateEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
}

// Human name validation function
function validateHumanName(name) {
    // Allow letters, spaces, hyphens, apostrophes, and periods
    // Must be at least 2 characters and not exceed 50 characters
    const nameRegex = /^[a-zA-Z\s\-'\.]{2,50}$/;
    return nameRegex.test(name.trim());
}

// Date range validation function
function validateDateRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const today = new Date();
    
    // Check if dates are valid
    if (isNaN(start.getTime()) || isNaN(end.getTime())) {
        return { isValid: false, message: "Please enter valid dates." };
    }
    
    // Check if start date is not in the past
    if (start < today) {
        return { isValid: false, message: "Start date cannot be in the past." };
    }
    
    // Check if end date is after start date
    if (end <= start) {
        return { isValid: false, message: "End date must be after start date." };
    }
    
    // Check if date range is reasonable (not more than 5 years)
    const diffTime = end - start;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const maxDays = 365 * 5; // 5 years
    
    if (diffDays > maxDays) {
        return { isValid: false, message: "Date range cannot exceed 5 years." };
    }
    
    return { isValid: true, message: "Date range is valid." };
}

// Real-time email validation
function validateEmailField(emailField) {
    const email = emailField.value.trim();
    const feedbackElement = emailField.parentNode.querySelector('.invalid-feedback');
    
    if (email === '') {
        emailField.classList.add('is-invalid');
        emailField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Email address is required.';
        }
        return false;
    } else if (!validateEmail(email)) {
        emailField.classList.add('is-invalid');
        emailField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Please enter a valid email address.';
        }
        return false;
    } else {
        emailField.classList.remove('is-invalid');
        emailField.classList.add('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = '';
        }
        return true;
    }
}

// Real-time name validation
function validateNameField(nameField) {
    const name = nameField.value.trim();
    const feedbackElement = nameField.parentNode.querySelector('.invalid-feedback');
    
    if (name === '') {
        nameField.classList.add('is-invalid');
        nameField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Name is required.';
        }
        return false;
    } else if (!validateHumanName(name)) {
        nameField.classList.add('is-invalid');
        nameField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Please enter a valid name (letters, spaces, hyphens, apostrophes, and periods only).';
        }
        return false;
    } else {
        nameField.classList.remove('is-invalid');
        nameField.classList.add('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = '';
        }
        return true;
    }
}

// Date range validation for service form
function validateServiceDates() {
    const startDateField = document.getElementById('startDate');
    const endDateField = document.getElementById('endDate');
    
    if (!startDateField || !endDateField) return true;
    
    const startDate = startDateField.value;
    const endDate = endDateField.value;
    
    if (startDate && endDate) {
        const result = validateDateRange(startDate, endDate);
        
        if (!result.isValid) {
            endDateField.classList.add('is-invalid');
            endDateField.classList.remove('is-valid');
            const feedbackElement = endDateField.parentNode.querySelector('.invalid-feedback');
            if (feedbackElement) {
                feedbackElement.textContent = result.message;
            }
            return false;
        } else {
            endDateField.classList.remove('is-invalid');
            endDateField.classList.add('is-valid');
            const feedbackElement = endDateField.parentNode.querySelector('.invalid-feedback');
            if (feedbackElement) {
                feedbackElement.textContent = '';
            }
            return true;
        }
    }
    
    return true;
}

// Password strength validation
function validatePassword(password) {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    const errors = [];
    
    if (password.length < minLength) {
        errors.push(`Password must be at least ${minLength} characters long.`);
    }
    if (!hasUpperCase) {
        errors.push('Password must contain at least one uppercase letter.');
    }
    if (!hasLowerCase) {
        errors.push('Password must contain at least one lowercase letter.');
    }
    if (!hasNumbers) {
        errors.push('Password must contain at least one number.');
    }
    if (!hasSpecialChar) {
        errors.push('Password must contain at least one special character.');
    }
    
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// Password confirmation validation
function validatePasswordConfirmation(password, confirmPassword) {
    return password === confirmPassword;
}

// Initialize form validation
function initializeFormValidation() {
    // Add event listeners for real-time validation
    const emailFields = document.querySelectorAll('input[type="email"]');
    const nameFields = document.querySelectorAll('input[name="firstName"], input[name="lastName"]');
    const passwordFields = document.querySelectorAll('input[name="password"]');
    const confirmPasswordFields = document.querySelectorAll('input[name="confirmPassword"]');
    
    // Email validation
    emailFields.forEach(field => {
        field.addEventListener('blur', () => validateEmailField(field));
        field.addEventListener('input', () => validateEmailField(field));
    });
    
    // Name validation
    nameFields.forEach(field => {
        field.addEventListener('blur', () => validateNameField(field));
        field.addEventListener('input', () => validateNameField(field));
    });
    
    // Password validation
    passwordFields.forEach(field => {
        field.addEventListener('blur', () => validatePasswordField(field));
        field.addEventListener('input', () => validatePasswordField(field));
    });
    
    // Confirm password validation
    confirmPasswordFields.forEach(field => {
        field.addEventListener('blur', () => validateConfirmPasswordField(field));
        field.addEventListener('input', () => validateConfirmPasswordField(field));
    });
    
    // Date validation for service form
    const startDateField = document.getElementById('startDate');
    const endDateField = document.getElementById('endDate');
    
    if (startDateField && endDateField) {
        startDateField.addEventListener('change', validateServiceDates);
        endDateField.addEventListener('change', validateServiceDates);
    }
}

// Password field validation
function validatePasswordField(passwordField) {
    const password = passwordField.value;
    const feedbackElement = passwordField.parentNode.querySelector('.invalid-feedback');
    
    if (password === '') {
        passwordField.classList.add('is-invalid');
        passwordField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Password is required.';
        }
        return false;
    }
    
    const result = validatePassword(password);
    
    if (!result.isValid) {
        passwordField.classList.add('is-invalid');
        passwordField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = result.errors.join(' ');
        }
        return false;
    } else {
        passwordField.classList.remove('is-invalid');
        passwordField.classList.add('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = '';
        }
        return true;
    }
}

// Confirm password field validation
function validateConfirmPasswordField(confirmPasswordField) {
    const confirmPassword = confirmPasswordField.value;
    const passwordField = document.querySelector('input[name="password"]');
    const feedbackElement = confirmPasswordField.parentNode.querySelector('.invalid-feedback');
    
    if (confirmPassword === '') {
        confirmPasswordField.classList.add('is-invalid');
        confirmPasswordField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Please confirm your password.';
        }
        return false;
    }
    
    if (passwordField && !validatePasswordConfirmation(passwordField.value, confirmPassword)) {
        confirmPasswordField.classList.add('is-invalid');
        confirmPasswordField.classList.remove('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = 'Passwords do not match.';
        }
        return false;
    } else {
        confirmPasswordField.classList.remove('is-invalid');
        confirmPasswordField.classList.add('is-valid');
        if (feedbackElement) {
            feedbackElement.textContent = '';
        }
        return true;
    }
}

// Form submission validation
function validateForm(form) {
    let isValid = true;
    
    // Validate all required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (field.type === 'email') {
            if (!validateEmailField(field)) isValid = false;
        } else if (field.name === 'firstName' || field.name === 'lastName') {
            if (!validateNameField(field)) isValid = false;
        } else if (field.name === 'password') {
            if (!validatePasswordField(field)) isValid = false;
        } else if (field.name === 'confirmPassword') {
            if (!validateConfirmPasswordField(field)) isValid = false;
        } else if (field.value.trim() === '') {
            field.classList.add('is-invalid');
            isValid = false;
        }
    });
    
    // Validate date range if present
    if (form.querySelector('#startDate') && form.querySelector('#endDate')) {
        if (!validateServiceDates()) isValid = false;
    }
    
    return isValid;
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeFormValidation();
    
    // Add form submission validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!validateForm(this)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
}); 