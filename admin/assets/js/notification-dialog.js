/**
 * Custom Notification Dialog Module
 * A beautiful, modern replacement for native alert() dialog
 * 
 * Usage:
 * showNotificationDialog({
 *   title: 'Success',
 *   message: 'Product added successfully!',
 *   type: 'success'  // 'success', 'error', 'warning', 'info'
 * });
 */

(function() {
  'use strict';

  // Dialog types configuration
  const DIALOG_TYPES = {
    success: {
      icon: 'check_circle',
      iconColor: 'text-green-600',
      bgColor: 'bg-green-100',
      buttonClass: 'bg-green-600 hover:bg-green-700 text-white'
    },
    error: {
      icon: 'error',
      iconColor: 'text-red-600',
      bgColor: 'bg-red-100',
      buttonClass: 'bg-red-600 hover:bg-red-700 text-white'
    },
    warning: {
      icon: 'warning',
      iconColor: 'text-yellow-600',
      bgColor: 'bg-yellow-100',
      buttonClass: 'bg-yellow-600 hover:bg-yellow-700 text-white'
    },
    info: {
      icon: 'info',
      iconColor: 'text-blue-600',
      bgColor: 'bg-blue-100',
      buttonClass: 'bg-blue-600 hover:bg-blue-700 text-white'
    }
  };

  /**
   * Show notification dialog
   * @param {Object} options - Dialog configuration
   * @returns {Promise<void>} - Resolves when dialog is closed
   */
  window.showNotificationDialog = function(options = {}) {
    return new Promise((resolve) => {
      // Default options
      const config = {
        title: options.title || 'Notification',
        message: options.message || '',
        type: options.type || 'info',
        okText: options.okText || 'OK',
        autoClose: options.autoClose || false,
        autoCloseDelay: options.autoCloseDelay || 3000
      };

      // Get type configuration
      const typeConfig = DIALOG_TYPES[config.type] || DIALOG_TYPES.info;

      // Create dialog HTML
      const dialogHTML = `
        <div id="notificationDialogBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4 animate-fadeIn">
          <div id="notificationDialogContent" class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform animate-slideUp">
            <!-- Icon -->
            <div class="flex justify-center pt-8 pb-4">
              <div class="${typeConfig.bgColor} w-20 h-20 rounded-full flex items-center justify-center">
                <span class="material-icons ${typeConfig.iconColor} text-5xl">${typeConfig.icon}</span>
              </div>
            </div>
            
            <!-- Title -->
            <div class="px-8 pb-2">
              <h3 class="text-2xl font-bold text-gray-800 text-center">${config.title}</h3>
            </div>
            
            <!-- Message -->
            <div class="px-8 pb-6">
              <p class="text-gray-600 text-center leading-relaxed">${config.message}</p>
            </div>
            
            <!-- Button -->
            <div class="px-8 pb-8">
              <button 
                id="notificationDialogOkBtn"
                class="w-full ${typeConfig.buttonClass} px-6 py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 active:scale-95 shadow-lg"
              >
                ${config.okText}
              </button>
            </div>
          </div>
        </div>
      `;

      // Add dialog to page
      const dialogContainer = document.createElement('div');
      dialogContainer.innerHTML = dialogHTML;
      document.body.appendChild(dialogContainer);

      // Get elements
      const backdrop = document.getElementById('notificationDialogBackdrop');
      const okBtn = document.getElementById('notificationDialogOkBtn');

      // Close dialog function
      const closeDialog = () => {
        backdrop.classList.add('animate-fadeOut');
        setTimeout(() => {
          document.body.removeChild(dialogContainer);
          resolve();
        }, 200);
      };

      // Event listeners
      okBtn.addEventListener('click', closeDialog);
      
      // Close on backdrop click
      backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) {
          closeDialog();
        }
      });

      // Close on Escape key
      const handleEscape = (e) => {
        if (e.key === 'Escape') {
          document.removeEventListener('keydown', handleEscape);
          closeDialog();
        }
      };
      document.addEventListener('keydown', handleEscape);

      // Auto-close if enabled
      if (config.autoClose) {
        setTimeout(() => {
          closeDialog();
        }, config.autoCloseDelay);
      }

      // Focus OK button
      setTimeout(() => okBtn.focus(), 100);
    });
  };

  // Add CSS animations
  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes fadeOut {
      from {
        opacity: 1;
      }
      to {
        opacity: 0;
      }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.2s ease-out;
    }

    .animate-fadeOut {
      animation: fadeOut 0.2s ease-in;
    }

    .animate-slideUp {
      animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
  `;
  document.head.appendChild(style);

  console.log('✅ Notification Dialog module loaded');
})();

