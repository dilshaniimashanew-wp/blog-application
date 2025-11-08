/**
 * BlogChain - Main JavaScript File
 * Interactive features and enhancements
 */

// ============================================
// DOCUMENT READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔗 Blog_Chain loaded successfully!');
    
    // Initialize all features
    initImagePreview();
    initFormValidation();
    initCharacterCounter();
    initSmoothScroll();
    initReadingTime();
    initDeleteConfirmation();
    initTooltips();
    initAnimations();
});

// ============================================
// IMAGE PREVIEW FOR BLOG UPLOADS
// Show preview before uploading
// ============================================
function initImagePreview() {
    const imageInput = document.getElementById('blog_image');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    showNotification('Please select a valid image file', 'error');
                    this.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('Image size must be less than 5MB', 'error');
                    this.value = '';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Create or update preview container
                    let preview = document.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'image-preview';
                        imageInput.parentNode.appendChild(preview);
                    }
                    
                    preview.innerHTML = `
                        <p style="margin-bottom: 10px; font-weight: 600; color: var(--primary);">
                            📸 Image Preview:
                        </p>
                        <img src="${event.target.result}" alt="Preview">
                        <button type="button" class="btn-remove-image" onclick="removeImagePreview()">
                            ❌ Remove Image
                        </button>
                    `;
                    preview.classList.add('active');
                    
                    showNotification('Image loaded successfully!', 'success');
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Remove image preview
function removeImagePreview() {
    const preview = document.querySelector('.image-preview');
    const imageInput = document.getElementById('blog_image');
    
    if (preview) {
        preview.remove();
    }
    
    if (imageInput) {
        imageInput.value = '';
    }
    
    showNotification('Image removed', 'info');
}

// ============================================
// FORM VALIDATION
// Real-time validation for forms
// ============================================
function initFormValidation() {
    // Title validation
    const titleInput = document.querySelector('input[name="title"]');
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            validateTitle(this);
        });
    }
    
    // Content validation
    const contentTextarea = document.querySelector('textarea[name="content"]');
    if (contentTextarea) {
        contentTextarea.addEventListener('input', function() {
            validateContent(this);
        });
    }
    
    // Password match validation
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector('input[name="confirm_password"]');
    
    if (password && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (this.value !== password.value) {
                this.style.borderColor = 'var(--danger)';
                showFieldError(this, 'Passwords do not match');
            } else {
                this.style.borderColor = 'var(--success)';
                clearFieldError(this);
            }
        });
    }
}

// Validate title
function validateTitle(input) {
    const value = input.value.trim();
    
    if (value.length < 5) {
        input.style.borderColor = 'var(--danger)';
        showFieldError(input, 'Title must be at least 5 characters');
    } else if (value.length > 255) {
        input.style.borderColor = 'var(--warning)';
        showFieldError(input, 'Title is too long (max 255 characters)');
    } else {
        input.style.borderColor = 'var(--success)';
        clearFieldError(input);
    }
}

// Validate content
function validateContent(textarea) {
    const value = textarea.value.trim();
    
    if (value.length < 20) {
        textarea.style.borderColor = 'var(--danger)';
        showFieldError(textarea, 'Content must be at least 20 characters');
    } else {
        textarea.style.borderColor = 'var(--success)';
        clearFieldError(textarea);
    }
}

// Show field error
function showFieldError(field, message) {
    let errorDiv = field.parentNode.querySelector('.field-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = 'var(--danger)';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '5px';
        field.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

// Clear field error
function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// ============================================
// CHARACTER COUNTER
// Show remaining characters for inputs
// ============================================
function initCharacterCounter() {
    const titleInput = document.querySelector('input[name="title"]');
    const contentTextarea = document.querySelector('textarea[name="content"]');
    
    if (titleInput) {
        addCharacterCounter(titleInput, 255);
    }
    
    if (contentTextarea) {
        addCharacterCounter(contentTextarea, 5000);
    }
}

function addCharacterCounter(element, maxLength) {
    const counter = document.createElement('div');
    counter.className = 'character-counter';
    counter.style.textAlign = 'right';
    counter.style.fontSize = '0.85rem';
    counter.style.color = 'var(--text-muted)';
    counter.style.marginTop = '5px';
    
    element.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = element.value.length;
        const remaining = maxLength - length;
        counter.textContent = `${length} / ${maxLength} characters`;
        
        if (remaining < 50) {
            counter.style.color = 'var(--danger)';
        } else if (remaining < 100) {
            counter.style.color = 'var(--warning)';
        } else {
            counter.style.color = 'var(--text-muted)';
        }
    }
    
    element.addEventListener('input', updateCounter);
    updateCounter(); // Initial count
}

// ============================================
// SMOOTH SCROLL
// Smooth scrolling for anchor links
// ============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// ============================================
// READING TIME CALCULATOR
// Estimate reading time for blog posts
// ============================================
function initReadingTime() {
    const content = document.querySelector('.blog-view-content');
    const metaDiv = document.querySelector('.blog-view-meta');
    
    if (content && metaDiv) {
        const text = content.textContent || content.innerText;
        const wordCount = text.trim().split(/\s+/).length;
        const readingTime = Math.ceil(wordCount / 200); // Average reading speed: 200 words/min
        
        const timeSpan = document.createElement('span');
        timeSpan.innerHTML = `📖 ${readingTime} min read`;
        timeSpan.style.color = 'var(--primary)';
        timeSpan.style.fontWeight = '600';
        metaDiv.appendChild(timeSpan);
    }
}

// ============================================
// DELETE CONFIRMATION
// Enhanced confirmation dialog
// ============================================
function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('a[href*="delete.php"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            // Create custom confirmation modal
            const modal = createConfirmModal(
                'Delete Blog Post?',
                'This action cannot be undone. Are you sure you want to delete this blog post?',
                function() {
                    window.location.href = url;
                }
            );
            
            document.body.appendChild(modal);
        });
    });
}

// Create confirmation modal
function createConfirmModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'custom-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <h2>${title}</h2>
            <p>${message}</p>
            <div class="modal-actions">
                <button class="btn btn-danger" id="confirm-btn">Delete</button>
                <button class="btn btn-secondary" id="cancel-btn">Cancel</button>
            </div>
        </div>
    `;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .custom-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }
        .modal-content {
            position: relative;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            max-width: 500px;
            margin: 0 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        .modal-content h2 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        .modal-content p {
            margin-bottom: 1.5rem;
            color: var(--text-light);
            line-height: 1.6;
        }
        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(50px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
    
    // Event listeners
    modal.querySelector('#confirm-btn').addEventListener('click', () => {
        onConfirm();
        modal.remove();
    });
    
    modal.querySelector('#cancel-btn').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.querySelector('.modal-overlay').addEventListener('click', () => {
        modal.remove();
    });
    
    return modal;
}

// ============================================
// NOTIFICATION SYSTEM
// Show toast notifications
// ============================================
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `toast-notification toast-${type}`;
    
    const icons = {
        success: '✓',
        error: '⚠️',
        info: 'ℹ️',
        warning: '⚡'
    };
    
    notification.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <span class="toast-message">${message}</span>
    `;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 10000;
            animation: slideInRight 0.3s ease, slideOutRight 0.3s ease 2.7s;
            min-width: 300px;
        }
        .toast-success { border-left: 4px solid var(--success); }
        .toast-error { border-left: 4px solid var(--danger); }
        .toast-info { border-left: 4px solid var(--info); }
        .toast-warning { border-left: 4px solid var(--warning); }
        .toast-icon { font-size: 1.5rem; }
        .toast-message { font-weight: 500; color: var(--dark); }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// ============================================
// TOOLTIPS
// Add helpful tooltips to elements
// ============================================
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.cssText = `
                position: absolute;
                background: var(--dark);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-size: 0.85rem;
                white-space: nowrap;
                z-index: 1000;
                pointer-events: none;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            }, { once: true });
        });
    });
}

// ============================================
// SCROLL ANIMATIONS
// Animate elements on scroll
// ============================================
function initAnimations() {
    const animatedElements = document.querySelectorAll('.blog-card, .auth-box, .editor-box');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
            }
        });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        observer.observe(element);
    });
    
    // Add animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Format date to readable format
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Truncate text
function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================
// EXPORT FUNCTIONS (if needed)
// ============================================
window.BlogChain = {
    showNotification,
    removeImagePreview,
    formatDate,
    truncateText
};

console.log('🔗 Blog_Chain JavaScript initialized!');