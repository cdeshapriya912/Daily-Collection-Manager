<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Verify OTP - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Daily Collection Manager - OTP Verification">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#10b981",
              "green-bright": "#99EF02",
              "green-dark": "#16a34a",
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
            },
            borderRadius: {
              lg: "0.75rem",
            },
          },
        },
      };
    </script>
    <style>
      body { 
        font-family: 'Inter', sans-serif; 
        background: #22c55e;
        min-height: 100vh;
      }
      
      .text-green-dark {
        --tw-text-opacity: 1;
        color: rgb(13 71 34);
      }
      
      .text-green-darker {
        --tw-text-opacity: 1;
        color: rgb(8 50 20);
      }
      
      .font-bold {
        font-weight: 700;
      }
      
      .text-lg {
        font-size: 1.5rem;
        line-height: 1.75rem;
      }
      
      .text-xl {
        font-size: 1.75rem;
        line-height: 2rem;
      }
    </style>
  </head>
  <body>
    <!-- Main Content -->
    <main class="min-h-screen flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="material-icons text-green-600 text-5xl">email</span>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Verify Your Email</h1>
          <p class="text-white/80 text-lg">Enter the OTP code sent to your email</p>
        </div>

        <!-- OTP Form -->
        <div class="bg-white rounded-2xl p-8 shadow-xl">
          <form id="otpForm" class="space-y-6">
            <!-- Email Display -->
            <div class="text-center mb-4">
              <p class="text-gray-600 text-sm">OTP sent to:</p>
              <p class="text-gray-800 font-semibold" id="emailDisplay">admin@example.com</p>
            </div>

            <!-- OTP Input Field -->
            <div>
              <label for="otpCode" class="block text-gray-700 text-sm font-medium mb-2">
                Enter OTP Code
              </label>
              <div class="relative">
                <span class="material-icons absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                  vpn_key
                </span>
                <input 
                  type="text" 
                  id="otpCode" 
                  name="otp"
                  placeholder="000000" 
                  maxlength="6"
                  pattern="[0-9]{6}"
                  required
                  class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-lg text-2xl text-center tracking-widest font-bold focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                >
              </div>
              <p class="text-gray-500 text-xs mt-2 text-center">Enter 6-digit code</p>
            </div>

            <!-- Timer -->
            <div class="text-center">
              <p class="text-gray-600 text-sm">
                Code expires in: 
                <span id="timer" class="text-green-600 font-bold text-lg">02:00</span>
              </p>
            </div>

            <!-- Verify Button -->
            <button 
              type="submit"
              id="verifyBtn"
              class="w-full bg-green-bright text-black px-6 py-4 rounded-full font-bold text-lg hover:opacity-90 transition-opacity shadow-lg"
            >
              Verify OTP
            </button>

            <!-- Resend OTP -->
            <div class="text-center">
              <button 
                type="button"
                id="resendBtn"
                class="text-sm text-gray-600 hover:text-green-600 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
              >
                Resend OTP (<span id="resendTimer">120</span>s)
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white p-6 rounded-lg max-w-sm w-full text-center">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
          <span class="material-icons text-red-600 text-3xl">error</span>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Verification Failed</h3>
        <p class="text-gray-600 mb-4" id="errorMessage">Invalid OTP code</p>
        <button id="closeErrorModal" class="w-full bg-green-bright text-black px-4 py-3 rounded-full font-bold">
          OK
        </button>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-4"></div>
        <p class="text-gray-700 font-medium" id="loadingText">Verifying...</p>
      </div>
    </div>

    <script>
      let timerInterval = null;
      let resendTimerInterval = null;
      let timeLeft = 120; // 2 minutes
      let resendTimeLeft = 120;

      // Get email from localStorage or use default
      const userEmail = localStorage.getItem('login_email') || 'admin@example.com';
      document.getElementById('emailDisplay').textContent = userEmail;

      // Format time as MM:SS
      function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
      }

      // Start countdown timer
      function startTimer() {
        timerInterval = setInterval(() => {
          timeLeft--;
          document.getElementById('timer').textContent = formatTime(timeLeft);
          
          if (timeLeft <= 0) {
            clearInterval(timerInterval);
            document.getElementById('timer').textContent = '00:00';
            document.getElementById('timer').parentElement.innerHTML = 
              '<p class="text-red-600 text-sm font-semibold">OTP has expired. Please request a new code.</p>';
          }
        }, 1000);
      }

      // Start resend timer
      function startResendTimer() {
        const resendBtn = document.getElementById('resendBtn');
        resendBtn.disabled = true;
        resendTimeLeft = 120;

        resendTimerInterval = setInterval(() => {
          resendTimeLeft--;
          document.getElementById('resendTimer').textContent = resendTimeLeft;
          
          if (resendTimeLeft <= 0) {
            clearInterval(resendTimerInterval);
            resendBtn.disabled = false;
            document.getElementById('resendTimer').parentElement.innerHTML = 'Resend OTP';
          }
        }, 1000);
      }

      // Send OTP
      async function sendOTP() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingText').textContent = 'Sending OTP...';
        
        try {
          const response = await fetch('api/send-otp.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              email: userEmail
            })
          });

          const result = await response.json();
          
          document.getElementById('loadingOverlay').classList.add('hidden');
          
          if (result.success) {
            // Reset timer
            if (timerInterval) clearInterval(timerInterval);
            timeLeft = 120;
            
            // Update timer display
            const timerParent = document.getElementById('timer').parentElement;
            timerParent.innerHTML = 
              '<p class="text-gray-600 text-sm">Code expires in: <span id="timer" class="text-green-600 font-bold text-lg">02:00</span></p>';
            startTimer();
            
            // Reset OTP input
            document.getElementById('otpCode').value = '';
            
            // Start resend timer
            if (resendTimerInterval) clearInterval(resendTimerInterval);
            startResendTimer();
            
            console.log('OTP sent:', result.otp); // For development only - remove in production
          } else {
            showError(result.error || 'Failed to send OTP');
          }
        } catch (error) {
          document.getElementById('loadingOverlay').classList.add('hidden');
          showError('Network error. Please try again.');
          console.error('OTP send error:', error);
        }
      }

      // Verify OTP
      document.getElementById('otpForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const otp = document.getElementById('otpCode').value.trim();
        
        if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
          showError('Please enter a valid 6-digit OTP code');
          return;
        }
        
        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingText').textContent = 'Verifying...';
        
        try {
          const response = await fetch('api/verify-otp.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              otp: otp
            })
          });

          const result = await response.json();
          
          document.getElementById('loadingOverlay').classList.add('hidden');
          
          if (result.success) {
            // Redirect to dashboard
            window.location.href = 'dashboard.php';
          } else {
            showError(result.error || 'Invalid OTP code');
          }
        } catch (error) {
          document.getElementById('loadingOverlay').classList.add('hidden');
          showError('Network error. Please try again.');
          console.error('OTP verify error:', error);
        }
      });

      // Resend OTP
      document.getElementById('resendBtn').addEventListener('click', function() {
        sendOTP();
      });

      function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
      }

      // Close error modal
      document.getElementById('closeErrorModal').addEventListener('click', function() {
        document.getElementById('errorModal').classList.add('hidden');
      });

      // Auto-format OTP input (numbers only)
      document.getElementById('otpCode').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6) {
          // Auto-submit when 6 digits entered
          // document.getElementById('otpForm').dispatchEvent(new Event('submit'));
        }
      });

      // Auto-focus OTP field
      document.getElementById('otpCode').focus();

      // Send OTP automatically on page load
      window.addEventListener('DOMContentLoaded', function() {
        sendOTP();
      });

      // Start timers on page load (will be started after OTP is sent)
    </script>
  </body>
</html>

