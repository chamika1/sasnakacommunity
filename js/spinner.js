// Spinner Management
class SpinnerManager {
    constructor() {
        this.spinner = document.querySelector('.spinner-overlay');
        this.spinnerText = this.spinner.querySelector('.spinner-text');
        this.loadingMessages = [
            'Loading awesome content...',
            'Getting things ready...',
            'Making it perfect...',
            'Almost there...',
            'Preparing your experience...',
            'Just a moment...'
        ];
        this.setupListeners();
    }

    show() {
        // Set random loading message
        this.spinnerText.textContent = this.loadingMessages[
            Math.floor(Math.random() * this.loadingMessages.length)
        ];
        this.spinner.classList.add('active');
    }

    hide() {
        this.spinner.classList.remove('active');
    }

    setupListeners() {
        // Setup form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form.hasAttribute('data-no-spinner')) {
                this.show();
            }
        });

        // Setup link clicks
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && 
                !link.hasAttribute('data-no-spinner') && 
                !link.getAttribute('href').startsWith('#') &&
                !link.getAttribute('href').includes('javascript:')) {
                this.show();
            }
        });

        // Setup AJAX if jQuery is available
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ajaxStart(() => this.show());
            jQuery(document).ajaxStop(() => this.hide());
        }

        // Hide spinner when page is loaded
        window.addEventListener('load', () => this.hide());

        // Show spinner when leaving page
        window.addEventListener('beforeunload', () => this.show());
    }
}

// Initialize spinner when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.spinnerManager = new SpinnerManager();
    });
} else {
    window.spinnerManager = new SpinnerManager();
} 