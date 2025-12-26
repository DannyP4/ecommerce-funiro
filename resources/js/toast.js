/**
 * Toast Notification System
 * Displays beautiful toast notifications for success, error, warning, and info messages
 */

class Toast {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('toast-container');
        }
    }

    show(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} toast-enter`;
        
        const icon = this.getIcon(type);
        
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        this.container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-show');
        }, 10);
        
        // Auto remove after duration
        setTimeout(() => {
            this.hide(toast);
        }, duration);
    }

    hide(toast) {
        toast.classList.remove('toast-show');
        toast.classList.add('toast-exit');
        
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }

    getIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-exclamation-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };
        return icons[type] || icons.info;
    }

    success(message, duration = 3000) {
        this.show(message, 'success', duration);
    }

    error(message, duration = 4000) {
        this.show(message, 'error', duration);
    }

    warning(message, duration = 3500) {
        this.show(message, 'warning', duration);
    }

    info(message, duration = 3000) {
        this.show(message, 'info', duration);
    }
}

// Initialize global toast instance
window.toast = new Toast();

// Auto-show Laravel flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('[data-flash-message]');
    flashMessages.forEach(function(element) {
        const message = element.getAttribute('data-flash-message');
        const type = element.getAttribute('data-flash-type') || 'info';
        window.toast.show(message, type);
        element.remove();
    });
});
