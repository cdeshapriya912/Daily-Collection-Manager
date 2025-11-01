/**
 * Custom Confirmation Dialog Module
 * A beautiful, reusable confirmation dialog for the entire application
 * 
 * Usage:
 * const result = await showConfirmDialog({
 *   title: 'Delete User?',
 *   message: 'Are you sure you want to delete this user? This action cannot be undone.',
 *   confirmText: 'Delete',
 *   cancelText: 'Cancel',
 *   type: 'danger' // 'danger', 'warning', 'info', 'success'
 * });
 * 
 * if (result) {
 *   // User clicked confirm
 * }
 */

class ConfirmationDialog {
  constructor() {
    this.dialog = null;
    this.resolveCallback = null;
    this.createDialog();
  }

  createDialog() {
    // Create dialog HTML
    const dialogHTML = `
      <div id="confirmDialog" class="fixed inset-0 z-[9999] hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" id="confirmBackdrop"></div>
        
        <!-- Dialog -->
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="confirmDialogContent">
              <!-- Icon -->
              <div class="p-6 pb-4">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full" id="confirmIcon">
                  <span class="material-icons text-4xl" id="confirmIconText">help_outline</span>
                </div>
              </div>
              
              <!-- Content -->
              <div class="px-6 pb-4 text-center">
                <h3 class="text-xl font-bold text-heading-light mb-2" id="confirmTitle">Confirm Action</h3>
                <p class="text-text-light" id="confirmMessage">Are you sure you want to proceed?</p>
              </div>
              
              <!-- Actions -->
              <div class="px-6 pb-6 flex gap-3">
                <button 
                  type="button" 
                  id="confirmCancelBtn"
                  class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300"
                >
                  Cancel
                </button>
                <button 
                  type="button" 
                  id="confirmConfirmBtn"
                  class="flex-1 px-4 py-3 rounded-lg text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                >
                  Confirm
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    // Insert into body
    const div = document.createElement('div');
    div.innerHTML = dialogHTML;
    document.body.appendChild(div.firstElementChild);

    // Get references
    this.dialog = document.getElementById('confirmDialog');
    this.backdrop = document.getElementById('confirmBackdrop');
    this.content = document.getElementById('confirmDialogContent');
    this.icon = document.getElementById('confirmIcon');
    this.iconText = document.getElementById('confirmIconText');
    this.title = document.getElementById('confirmTitle');
    this.message = document.getElementById('confirmMessage');
    this.cancelBtn = document.getElementById('confirmCancelBtn');
    this.confirmBtn = document.getElementById('confirmConfirmBtn');

    // Attach event listeners
    this.cancelBtn.addEventListener('click', () => this.close(false));
    this.backdrop.addEventListener('click', () => this.close(false));
    this.confirmBtn.addEventListener('click', () => this.close(true));

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !this.dialog.classList.contains('hidden')) {
        this.close(false);
      }
    });
  }

  show(options = {}) {
    return new Promise((resolve) => {
      this.resolveCallback = resolve;

      // Set options with defaults
      const config = {
        title: options.title || 'Confirm Action',
        message: options.message || 'Are you sure you want to proceed?',
        confirmText: options.confirmText || 'Confirm',
        cancelText: options.cancelText || 'Cancel',
        type: options.type || 'warning' // danger, warning, info, success
      };

      // Update content
      this.title.textContent = config.title;
      this.message.textContent = config.message;
      this.cancelBtn.textContent = config.cancelText;
      this.confirmBtn.textContent = config.confirmText;

      // Set colors and icon based on type
      this.applyTheme(config.type);

      // Show dialog with animation
      this.dialog.classList.remove('hidden');
      
      // Trigger animation
      requestAnimationFrame(() => {
        this.content.classList.remove('scale-95', 'opacity-0');
        this.content.classList.add('scale-100', 'opacity-100');
      });

      // Focus confirm button
      setTimeout(() => this.confirmBtn.focus(), 100);
    });
  }

  applyTheme(type) {
    // Remove all theme classes
    this.icon.className = 'mx-auto flex items-center justify-center h-16 w-16 rounded-full';
    this.confirmBtn.className = 'flex-1 px-4 py-3 rounded-lg text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

    switch (type) {
      case 'danger':
        this.icon.classList.add('bg-red-100');
        this.iconText.classList.add('text-red-600');
        this.iconText.textContent = 'error_outline';
        this.confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
        break;
      
      case 'warning':
        this.icon.classList.add('bg-amber-100');
        this.iconText.classList.add('text-amber-600');
        this.iconText.textContent = 'warning';
        this.confirmBtn.classList.add('bg-amber-600', 'hover:bg-amber-700', 'focus:ring-amber-500');
        break;
      
      case 'info':
        this.icon.classList.add('bg-blue-100');
        this.iconText.classList.add('text-blue-600');
        this.iconText.textContent = 'info';
        this.confirmBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
        break;
      
      case 'success':
        this.icon.classList.add('bg-green-100');
        this.iconText.classList.add('text-green-600');
        this.iconText.textContent = 'check_circle';
        this.confirmBtn.classList.add('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');
        break;
      
      default:
        this.icon.classList.add('bg-gray-100');
        this.iconText.classList.add('text-gray-600');
        this.iconText.textContent = 'help_outline';
        this.confirmBtn.classList.add('bg-gray-600', 'hover:bg-gray-700', 'focus:ring-gray-500');
    }
  }

  close(result) {
    // Animate out
    this.content.classList.remove('scale-100', 'opacity-100');
    this.content.classList.add('scale-95', 'opacity-0');

    // Hide after animation
    setTimeout(() => {
      this.dialog.classList.add('hidden');
      if (this.resolveCallback) {
        this.resolveCallback(result);
        this.resolveCallback = null;
      }
    }, 300);
  }
}

// Create single global instance
const confirmDialogInstance = new ConfirmationDialog();

/**
 * Show confirmation dialog
 * @param {Object} options - Dialog options
 * @param {string} options.title - Dialog title
 * @param {string} options.message - Dialog message
 * @param {string} options.confirmText - Confirm button text
 * @param {string} options.cancelText - Cancel button text
 * @param {string} options.type - Dialog type: 'danger', 'warning', 'info', 'success'
 * @returns {Promise<boolean>} - Resolves to true if confirmed, false if cancelled
 */
window.showConfirmDialog = function(options) {
  return confirmDialogInstance.show(options);
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { showConfirmDialog: window.showConfirmDialog };
}



