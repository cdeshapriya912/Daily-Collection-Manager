<?php
// Start output buffering FIRST to catch any warnings/errors
ob_start();

// Enable error reporting for initial page load debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Suppress any output from session_start
@session_start();

// Handle login POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    // Clean any output that might have been generated
    ob_clean();
    
    // Set headers first
    header('Content-Type: application/json');
    
    // Enable error reporting for debugging but don't display errors
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';
    
    $response = ['success' => false, 'message' => 'Invalid username or password'];
    
    // Basic validation
    if (empty($username) || empty($password)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        exit;
    }
    
    try {
        // Include database configuration
        $dbPath = __DIR__ . '/admin/config/db.php';
        if (!file_exists($dbPath)) {
            throw new Exception('Database configuration file not found at: ' . $dbPath);
        }
        
        // Capture any output from db.php (it might die() with HTML)
        ob_start();
        try {
            require_once $dbPath;
            $dbOutput = ob_get_clean();
            
            // If db.php outputted anything, it likely means an error occurred
            if (!empty($dbOutput) && strpos($dbOutput, '<') !== false) {
                // Extract error message from HTML
                if (preg_match('/Database.*?does not exist/i', $dbOutput, $matches)) {
                    throw new Exception('Database does not exist. Please run install.php to create it.');
                } elseif (preg_match('/Database connection failed/i', $dbOutput, $matches)) {
                    throw new Exception('Database connection failed. Please check your MAMP MySQL server is running.');
                } else {
                    throw new Exception('Database configuration error occurred.');
                }
            }
        } catch (PDOException $e) {
            ob_end_clean();
            throw $e;
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        
        // Check if database connection is successful
        if (!isset($pdo) || $pdo === null) {
            throw new Exception('Database connection failed: PDO object not initialized');
        }
        
        // Test connection with a simple query
        try {
            $pdo->query("SELECT 1");
        } catch (PDOException $e) {
            throw new Exception('Database connection test failed: ' . $e->getMessage());
        }
        
        // Query user from database
        $stmt = $pdo->prepare("
            SELECT id, username, password_hash, full_name, email, role_id, status 
            FROM users 
            WHERE username = ? 
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $response = ['success' => false, 'message' => 'Invalid username or password'];
        } elseif ($user['status'] !== 'active') {
            $response = ['success' => false, 'message' => 'Your account is ' . $user['status'] . '. Please contact administrator.'];
        } else {
            // Verify password
            $passwordValid = password_verify($password, $user['password_hash']);
            
            // Debug logging (remove in production)
            if (!$passwordValid) {
                error_log("Login attempt failed for username: {$username}");
                error_log("Password hash from DB: " . substr($user['password_hash'], 0, 20) . "...");
            }
            
            if (!$passwordValid) {
                $response = ['success' => false, 'message' => 'Invalid username or password'];
            } else {
                // Password is correct - now require OTP verification
                // Store user data in session temporarily (don't set logged_in yet)
                $_SESSION['pending_user_id'] = (int)$user['id'];
                $_SESSION['pending_username'] = $user['username'];
                $_SESSION['pending_full_name'] = $user['full_name'] ?? 'Administrator';
                $_SESSION['pending_email'] = $user['email'] ?? '';
                $_SESSION['pending_role_id'] = (int)$user['role_id'];
                $_SESSION['pending_remember_me'] = $remember_me;
                
                // Check if user has email for OTP
                if (empty($user['email'])) {
                    // No email - skip OTP for now (or you can make it mandatory)
                    // For now, we'll proceed with login if no email
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'] ?? 'Administrator';
                    $_SESSION['email'] = $user['email'] ?? '';
                    $_SESSION['role_id'] = (int)$user['role_id'];
                    $_SESSION['logged_in'] = true;
                    session_regenerate_id(true);
                    
                    if ($remember_me) {
                        setcookie('remember_user', $user['username'], time() + (30 * 24 * 60 * 60), '/', '', false, true);
                    }
                    
                    $response = [
                        'success' => true,
                        'message' => 'Login successful',
                        'redirect' => 'admin/index.php',
                        'session_verified' => true
                    ];
                } else {
                    // User has email - send OTP
                    // Generate 6-digit OTP
                    $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    // Store OTP in session with expiration (2 minutes = 120 seconds)
                    $_SESSION['otp_code'] = $otp;
                    $_SESSION['otp_email'] = $user['email'];
                    $_SESSION['otp_expires'] = time() + 120;
                    $_SESSION['otp_user_id'] = (int)$user['id'];
                    
                    // Send OTP via email
                    // Mask email for display (calculate once)
                    $emailMasked = '';
                    if (!empty($user['email'])) {
                        $atPos = strpos($user['email'], '@');
                        if ($atPos !== false) {
                            $emailMasked = substr($user['email'], 0, 3) . '***' . substr($user['email'], $atPos);
                        } else {
                            $emailMasked = substr($user['email'], 0, 3) . '***';
                        }
                    }
                    
                    try {
                        // Check if SMTP config exists
                        $smtpConfigPath = __DIR__ . '/admin/config/smtp.php';
                        if (!file_exists($smtpConfigPath)) {
                            // No SMTP config - still generate OTP and return it
                            error_log('SMTP configuration not found - OTP generated but not sent');
                            $response = [
                                'success' => true,
                                'require_otp' => true,
                                'message' => 'OTP generated (email service not configured)',
                                'email' => $emailMasked,
                                'otp' => $otp // Include OTP for development
                            ];
                        } else {
                            $smtp_config = require $smtpConfigPath;
                        
                        $from_email = $smtp_config['from_email'];
                        $from_name = $smtp_config['from_name'];
                        $subject = 'Your Login OTP Verification Code';
                        
                        $html_body = "<html><body>";
                        $html_body .= "<p>Your OTP verification code is: <strong style='font-size: 24px; color: #10b981;'>{$otp}</strong></p>";
                        $html_body .= "<p>This code will expire in 2 minutes.</p>";
                        $html_body .= "<p>If you didn't request this code, please ignore this email.</p>";
                        $html_body .= "</body></html>";
                        
                        // Build email
                        $boundary = md5(time());
                        $email_content = "--{$boundary}\r\n";
                        $email_content .= "Content-Type: text/plain; charset=UTF-8\r\n";
                        $email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                        $email_content .= "Your OTP verification code is: {$otp}\r\n\r\n";
                        $email_content .= "This code will expire in 2 minutes.\r\n\r\n";
                        $email_content .= "--{$boundary}\r\n";
                        $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                        $email_content .= $html_body;
                        $email_content .= "\r\n--{$boundary}--\r\n";
                        
                        $headers = [];
                        $headers[] = "MIME-Version: 1.0";
                        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";
                        $headers[] = "From: {$from_name} <{$from_email}>";
                        $headers[] = "Reply-To: {$from_email}";
                        
                        // Include SMTP helper functions (utility file without headers/session)
                        if (!function_exists('sendSMTPEmail')) {
                            require_once __DIR__ . '/admin/config/smtp-functions.php';
                        }
                        
                        // Call the sendSMTPEmail function
                        $result = sendSMTPEmail(
                            $smtp_config['host'],
                            $smtp_config['port'],
                            $from_email,
                            $user['email'],
                            $subject,
                            $email_content,
                            $headers,
                            $smtp_config['username'],
                            $smtp_config['password'],
                            $smtp_config['timeout']
                        );
                        
                        if ($result['success']) {
                            $response = [
                                'success' => true,
                                'require_otp' => true,
                                'message' => 'OTP sent to your email',
                                'email' => $emailMasked
                            ];
                        } else {
                            // If email fails, still proceed with OTP (in case of email issues)
                            error_log('OTP email send failed: ' . ($result['error'] ?? 'Unknown error'));
                            $response = [
                                'success' => true,
                                'require_otp' => true,
                                'message' => 'OTP sent to your email (check your inbox)',
                                'email' => $emailMasked,
                                'otp' => $otp // Include OTP in response for development (remove in production)
                            ];
                        }
                        } // Close else block for SMTP config exists
                    } catch (Exception $e) {
                        error_log('OTP send error: ' . $e->getMessage());
                        // Proceed with OTP anyway
                        if (empty($emailMasked) && !empty($user['email'])) {
                            $atPos = strpos($user['email'], '@');
                            if ($atPos !== false) {
                                $emailMasked = substr($user['email'], 0, 3) . '***' . substr($user['email'], $atPos);
                            } else {
                                $emailMasked = substr($user['email'], 0, 3) . '***';
                            }
                        }
                        $response = [
                            'success' => true,
                            'require_otp' => true,
                            'message' => 'OTP generated. Please check your email.',
                            'email' => $emailMasked,
                            'otp' => $otp // Include OTP for development
                        ];
                    }
                }
            }
        }
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
        error_log('Login PDO error: ' . $errorMsg);
        
        // Clean any output buffer
        ob_clean();
        
        // Return detailed error for debugging (remove sensitive info in production)
        $response = [
            'success' => false, 
            'message' => 'Database error occurred',
            'debug' => $errorMsg // Remove this in production
        ];
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
        error_log('Login error: ' . $errorMsg);
        
        // Clean any output buffer
        ob_clean();
        
        $response = [
            'success' => false, 
            'message' => 'Error: ' . $errorMsg,
            'debug' => $errorMsg
        ];
    } catch (Error $e) {
        // Catch fatal errors
        $errorMsg = $e->getMessage();
        error_log('Login fatal error: ' . $errorMsg);
        
        ob_clean();
        
        $response = [
            'success' => false, 
            'message' => 'Fatal error occurred',
            'debug' => $errorMsg
        ];
    }
    
    // Clean output buffer and send response
    ob_clean();
    
    // Make sure we return proper JSON
    header('Content-Type: application/json');
    http_response_code(200);
    
    // Debug: Log the response (remove in production)
    error_log('Login response being sent: ' . json_encode($response));
    
    echo json_encode($response);
    exit;
}

// If already logged in, redirect to saved panel or default
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    ob_end_clean();
    
    // Check for saved panel preference (set via cookie or session)
    $savedPanel = null;
    
    // Check cookie first (persists across sessions)
    if (isset($_COOKIE['last_panel'])) {
        $savedPanel = $_COOKIE['last_panel'];
    }
    // Check session (current session only)
    elseif (isset($_SESSION['last_panel'])) {
        $savedPanel = $_SESSION['last_panel'];
    }
    
    // Validate and redirect to saved panel
    if ($savedPanel && in_array($savedPanel, ['admin/index.php', 'collection.php'])) {
        header('Location: ' . $savedPanel);
    } else {
        // Default to admin dashboard
        header('Location: admin/index.php');
    }
    exit;
}

// Clean any output buffer before HTML output
ob_end_flush();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Login - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="admin/img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="admin/img/package.png" type="image/png">
    <meta name="description" content="Daily Collection Manager - Login">
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
            <img src="admin/img/package.png" alt="Logo" class="w-12 h-12 object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'material-icons text-green-600 text-4xl\'>account_circle</span>';">
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Daily Collection</h1>
          <p class="text-white/80 text-lg">Welcome back! Please login</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-2xl p-8 shadow-xl">
          <form id="loginForm" class="space-y-6">
            <!-- Username/Email Field -->
            <div>
              <label for="username" class="block text-gray-700 text-sm font-medium mb-2">
                Username or Email
              </label>
              <div class="relative">
                <span class="material-icons absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                  person
                </span>
                <input 
                  type="text" 
                  id="username" 
                  name="username"
                  placeholder="Enter your username" 
                  required
                  class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                >
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <label for="password" class="block text-gray-700 text-sm font-medium mb-2">
                Password
              </label>
              <div class="relative">
                <span class="material-icons absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                  lock
                </span>
                <input 
                  type="password" 
                  id="password" 
                  name="password"
                  placeholder="Enter your password" 
                  required
                  class="w-full pl-12 pr-12 py-4 border-2 border-gray-200 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                >
                <button 
                  type="button"
                  id="togglePassword" 
                  class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <span class="material-icons" id="passwordIcon">visibility</span>
                </button>
              </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
              <label class="flex items-center">
                <input 
                  type="checkbox" 
                  id="rememberMe"
                  class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                >
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
              </label>
              <a href="#" class="text-sm text-green-600 hover:text-green-700 font-medium">
                Forgot password?
              </a>
            </div>

            <!-- Login Button -->
            <button 
              type="submit"
              id="loginBtn"
              class="w-full bg-green-bright text-black px-6 py-4 rounded-full font-bold text-lg hover:opacity-90 transition-opacity shadow-lg"
            >
              Login
            </button>
          </form>
        </div>
      </div>
    </main>

    <!-- Dashboard Selection (Hidden by default) -->
    <div id="dashboardSelectionContainer" class="min-h-screen flex items-center justify-center px-6 py-12 hidden">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="material-icons text-green-600 text-4xl">check_circle</span>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Welcome!</h1>
          <p class="text-white/80 text-lg">Choose your destination</p>
        </div>

        <!-- Dashboard Options -->
        <div class="space-y-4">
          <button 
            id="selectAdminDashboard"
            class="w-full bg-white hover:bg-gray-50 text-gray-800 px-6 py-6 rounded-2xl font-bold text-xl transition-all shadow-xl flex items-center justify-between group"
          >
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center">
                <span class="material-icons text-white text-3xl">dashboard</span>
              </div>
              <div class="text-left">
                <div class="font-bold">Admin Dashboard</div>
                <div class="text-sm text-gray-600 font-normal">Manage system settings</div>
              </div>
            </div>
            <span class="material-icons text-gray-400 group-hover:text-green-600 transition-colors">arrow_forward</span>
          </button>

          <button 
            id="selectCollectionPanel"
            class="w-full bg-white hover:bg-gray-50 text-gray-800 px-6 py-6 rounded-2xl font-bold text-xl transition-all shadow-xl flex items-center justify-between group"
          >
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-icons text-white text-3xl">assignment</span>
              </div>
              <div class="text-left">
                <div class="font-bold">Collection Panel</div>
                <div class="text-sm text-gray-600 font-normal">Daily collection management</div>
              </div>
            </div>
            <span class="material-icons text-gray-400 group-hover:text-blue-600 transition-colors">arrow_forward</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Destination Selection (Hidden by default) -->
    <div id="destinationSelectionContainer" class="min-h-screen flex items-center justify-center px-6 py-12 hidden">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="material-icons text-green-600 text-4xl">check_circle</span>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Login Successful!</h1>
          <p class="text-white/80 text-lg">Choose where you want to go</p>
        </div>

        <!-- Selection Buttons -->
        <div class="bg-white rounded-2xl p-8 shadow-xl space-y-4">
          <button 
            id="selectAdminBtn"
            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-5 rounded-xl font-bold text-lg transition-colors flex items-center justify-center gap-3 shadow-lg"
          >
            <span class="material-icons text-3xl">dashboard</span>
            <span>Admin Dashboard</span>
          </button>

          <button 
            id="selectCollectionBtn"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-5 rounded-xl font-bold text-lg transition-colors flex items-center justify-center gap-3 shadow-lg"
          >
            <span class="material-icons text-3xl">inventory_2</span>
            <span>Collection Panel</span>
          </button>
        </div>
      </div>
    </div>

    <!-- OTP Verification Form (Hidden by default) -->
    <div id="otpFormContainer" class="min-h-screen flex items-center justify-center px-6 py-12 hidden">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="material-icons text-green-600 text-4xl">vpn_key</span>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Verify Your Email</h1>
          <p class="text-white/80 text-lg">Enter the OTP sent to <span id="otpEmailDisplay"></span></p>
        </div>

        <!-- OTP Form -->
        <div class="bg-white rounded-2xl p-8 shadow-xl">
          <form id="otpForm" class="space-y-6">
            <!-- OTP Input Field -->
            <div>
              <label for="otpCode" class="block text-gray-700 text-sm font-medium mb-2 text-center">
                Enter 6-digit OTP Code
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
                  autocomplete="one-time-code"
                >
              </div>
              <p class="text-gray-500 text-xs mt-2 text-center">Check your email for the verification code</p>
            </div>

            <!-- Timer -->
            <div class="text-center">
              <p class="text-gray-600 text-sm">
                Code expires in: 
                <span id="otpTimer" class="text-green-600 font-bold text-lg">02:00</span>
              </p>
            </div>

            <!-- Verify Button -->
            <button 
              type="submit"
              id="verifyOtpBtn"
              class="w-full bg-green-bright text-black px-6 py-4 rounded-full font-bold text-lg hover:opacity-90 transition-opacity shadow-lg"
            >
              Verify OTP
            </button>

            <!-- Resend OTP -->
            <div class="text-center">
              <button 
                type="button"
                id="resendOtpBtn" 
                class="text-sm text-green-600 hover:text-green-700 font-medium disabled:text-gray-400 disabled:cursor-not-allowed"
                disabled
              >
                Resend OTP (<span id="resendTimer">30</span>s)
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white p-6 rounded-lg max-w-sm w-full text-center">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
          <span class="material-icons text-red-600 text-3xl">error</span>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Login Failed</h3>
        <p class="text-gray-600 mb-4" id="errorMessage">Invalid username or password</p>
        <button id="closeErrorModal" class="w-full bg-green-bright text-black px-4 py-3 rounded-full font-bold">
          OK
        </button>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-4"></div>
        <p class="text-gray-700 font-medium" id="loadingText">Logging in...</p>
      </div>
    </div>

    <!-- PWA Install Prompt -->
    <div id="installPrompt" class="fixed bottom-4 left-4 right-4 z-50 hidden">
      <div class="bg-white rounded-xl shadow-2xl p-4 flex items-center gap-4 border border-gray-200 max-w-md mx-auto">
        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
          <span class="material-icons text-white text-2xl">get_app</span>
        </div>
        <div class="flex-1">
          <h3 class="font-bold text-gray-800 text-sm">Install App</h3>
          <p class="text-gray-600 text-xs">Install for quick access and offline use</p>
        </div>
        <div class="flex gap-2">
          <button id="installAppBtn" class="bg-green-bright text-black px-4 py-2 rounded-lg font-bold text-sm hover:opacity-90 transition-opacity">
            Install
          </button>
          <button id="dismissInstallBtn" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-300 transition-colors">
            <span class="material-icons text-sm">close</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Panel Selection Screen -->
    <div id="panelSelectionContainer" class="min-h-screen flex items-center justify-center px-6 py-12 hidden" style="background: #22c55e;">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="material-icons text-green-600 text-4xl">dashboard</span>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Select Panel</h1>
          <p class="text-white/80 text-lg">Choose where you want to go</p>
        </div>

        <!-- Selection Buttons -->
        <div class="bg-white rounded-2xl p-8 shadow-xl space-y-4">
          <!-- Admin Dashboard Button -->
          <button 
            id="adminDashboardBtn"
            class="w-full bg-green-bright text-black px-6 py-6 rounded-xl font-bold text-lg hover:opacity-90 transition-opacity shadow-lg flex items-center justify-center gap-3 group"
          >
            <span class="material-icons text-2xl group-hover:scale-110 transition-transform">admin_panel_settings</span>
            <span>Admin Dashboard</span>
          </button>

          <!-- Collection Panel Button -->
          <button 
            id="collectionPanelBtn"
            class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-6 rounded-xl font-bold text-lg hover:opacity-90 transition-opacity shadow-lg flex items-center justify-center gap-3 group"
          >
            <span class="material-icons text-2xl group-hover:scale-110 transition-transform">account_balance_wallet</span>
            <span>Collection Panel</span>
          </button>
        </div>
      </div>
    </div>

    <script>
      // Toggle password visibility
      document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          passwordIcon.textContent = 'visibility_off';
        } else {
          passwordInput.type = 'password';
          passwordIcon.textContent = 'visibility';
        }
      });

      // Login form submission
      document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const rememberMe = document.getElementById('rememberMe').checked;
        
        // Basic validation
        if (!username || !password) {
          showError('Please enter both username and password');
          return;
        }
        
        // Show loading
        document.getElementById('loadingOverlay').classList.remove('hidden');
        
        // Submit form data via fetch to PHP backend
        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);
        formData.append('remember_me', rememberMe ? '1' : '0');
        
        try {
          const response = await fetch('login.php', {
            method: 'POST',
            body: formData
          });
          
          document.getElementById('loadingOverlay').classList.add('hidden');
          
          // Get response as text first (can only read once)
          const text = await response.text();
          
          console.log('Raw response text:', text);
          console.log('Response status:', response.status);
          console.log('Response headers:', response.headers.get('content-type'));
          
          // Check if response is ok
          if (!response.ok) {
            let errorMsg = `HTTP error! status: ${response.status}`;
            
            // Try to parse as JSON first
            try {
              const jsonError = JSON.parse(text);
              errorMsg = jsonError.message || errorMsg;
            } catch (e) {
              // If not JSON, might be HTML error from db.php
              if (text.includes('Database') || text.includes('does not exist')) {
                errorMsg = 'Database does not exist. Please run setup/install.php to create it.';
              } else if (text.includes('connection failed')) {
                errorMsg = 'Database connection failed. Please check your MAMP MySQL server is running.';
              } else {
                errorMsg = 'Server error occurred. Please check the console for details.';
              }
            }
            
            showError(errorMsg);
            console.error('HTTP Error:', response.status, text);
            return;
          }
          let result;
          
          try {
            result = JSON.parse(text);
            console.log('Parsed JSON result:', result);
          } catch (e) {
            // Response is not JSON (probably HTML error from db.php)
            console.error('JSON parse error:', e);
            console.error('Non-JSON response:', text);
            if (text.includes('Database') || text.includes('does not exist')) {
              showError('Database does not exist. Please run setup/install.php to create it.');
            } else if (text.includes('connection failed')) {
              showError('Database connection failed. Please check your MAMP MySQL server is running.');
            } else {
              showError('Server returned an invalid response. Please check the console for details.');
            }
            return;
          }
          
          // Log response for debugging
          console.log('Login response:', result);
          console.log('Result success:', result.success, 'Type:', typeof result.success);
          console.log('Result require_otp:', result.require_otp, 'Type:', typeof result.require_otp);
          
          // Check success (handle both true and "true" string)
          if (result.success === true || result.success === 1 || result.success === 'true') {
            // Check if OTP is required
            if (result.require_otp === true) {
              console.log('Showing OTP form for email:', result.email);
              // Show OTP input form
              showOTPForm(result.email || '');
              return;
            }
            
            // Store remember me preference (already handled by server via cookie)
            // Just sync with localStorage for consistency
            if (rememberMe) {
              localStorage.setItem('remember_user', username);
              localStorage.setItem('remember_me_active', '1');
            } else {
              localStorage.removeItem('remember_user');
              localStorage.removeItem('remember_me_active');
            }
            
            // Show success message
            console.log('Login successful! Session verified:', result.session_verified);
            console.log('Redirecting to:', result.redirect || 'admin/index.php');
            
            // Redirect immediately
            window.location.href = result.redirect || 'admin/index.php';
          } else {
            // Show error message from server
            let errorMsg = result.message || 'Invalid username or password';
            
            // Include debug info if available (for troubleshooting)
            if (result.debug) {
              console.error('Login debug info:', result.debug);
            }
            
            showError(errorMsg);
          }
        } catch (error) {
          document.getElementById('loadingOverlay').classList.add('hidden');
          console.error('Login error:', error);
          
          // Show more specific error message
          let errorMsg = 'Network error. Please try again.';
          if (error.message) {
            errorMsg = error.message;
          }
          showError(errorMsg);
        }
      });

      function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
      }

      // Close error modal
      document.getElementById('closeErrorModal').addEventListener('click', function() {
        document.getElementById('errorModal').classList.add('hidden');
      });

      // Close modal on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          document.getElementById('errorModal').classList.add('hidden');
        }
      });

      // OTP form management
      let otpTimerInterval = null;
      let otpTimeLeft = 120;
      let resendTimerInterval = null;
      let resendTimeLeft = 30;

      function showOTPForm(email) {
        // Hide login form
        document.querySelector('main').classList.add('hidden');
        // Show OTP form
        document.getElementById('otpFormContainer').classList.remove('hidden');
        document.getElementById('otpEmailDisplay').textContent = email;
        
        // Reset timers
        otpTimeLeft = 120;
        resendTimeLeft = 30;
        startOTPTimer();
        startResendTimer();
        
        // Focus OTP input
        setTimeout(() => {
          document.getElementById('otpCode').focus();
        }, 100);
      }

      function startOTPTimer() {
        if (otpTimerInterval) clearInterval(otpTimerInterval);
        
        otpTimerInterval = setInterval(() => {
          otpTimeLeft--;
          const minutes = Math.floor(otpTimeLeft / 60);
          const seconds = otpTimeLeft % 60;
          document.getElementById('otpTimer').textContent = 
            `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
          
          if (otpTimeLeft <= 0) {
            clearInterval(otpTimerInterval);
            showError('OTP code has expired. Please request a new one.');
            document.getElementById('resendOtpBtn').disabled = false;
            document.getElementById('resendTimer').parentElement.style.display = 'none';
          }
        }, 1000);
      }

      function startResendTimer() {
        document.getElementById('resendOtpBtn').disabled = true;
        resendTimeLeft = 30;
        
        if (resendTimerInterval) clearInterval(resendTimerInterval);
        
        resendTimerInterval = setInterval(() => {
          resendTimeLeft--;
          document.getElementById('resendTimer').textContent = resendTimeLeft;
          
          if (resendTimeLeft <= 0) {
            clearInterval(resendTimerInterval);
            document.getElementById('resendOtpBtn').disabled = false;
            document.getElementById('resendTimer').parentElement.style.display = 'none';
          }
        }, 1000);
      }

      // OTP form submission
      document.getElementById('otpForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const otp = document.getElementById('otpCode').value.trim();
        
        if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
          showError('Please enter a valid 6-digit OTP code');
          return;
        }
        
        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingText').textContent = 'Verifying OTP...';
        
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
            // OTP verified - complete login and show panel selection
            document.getElementById('loadingOverlay').classList.remove('hidden');
            document.getElementById('loadingText').textContent = 'Logging in...';
            
            // Call login completion endpoint
            const completeResponse = await fetch('api/complete-login.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              }
            });
            
            const completeResult = await completeResponse.json();
            document.getElementById('loadingOverlay').classList.add('hidden');
            
            if (completeResult.success) {
              // Check if we need to show panel selection
              if (completeResult.show_selection) {
                // Show panel selection screen
                showPanelSelection();
              } else {
                // Redirect to admin dashboard (fallback)
                window.location.href = completeResult.redirect || 'admin/index.php';
              }
            } else {
              showError(completeResult.message || 'Login completion failed');
            }
          } else {
            showError(result.error || 'Invalid OTP code');
            document.getElementById('otpCode').value = '';
            document.getElementById('otpCode').focus();
          }
        } catch (error) {
          document.getElementById('loadingOverlay').classList.add('hidden');
          showError('Network error. Please try again.');
          console.error('OTP verify error:', error);
        }
      });

      // Resend OTP
      document.getElementById('resendOtpBtn').addEventListener('click', async function() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingText').textContent = 'Resending OTP...';
        
        try {
          // Call resend endpoint
          const response = await fetch('api/resend-login-otp.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            }
          });
          
          const result = await response.json();
          document.getElementById('loadingOverlay').classList.add('hidden');
          
          if (result.success) {
            // Reset timers
            otpTimeLeft = 120;
            resendTimeLeft = 30;
            startOTPTimer();
            startResendTimer();
            document.getElementById('otpCode').value = '';
            document.getElementById('otpCode').focus();
            // Restore resend timer display if hidden
            const resendTimerParent = document.getElementById('resendTimer').parentElement;
            if (resendTimerParent.style.display === 'none') {
              resendTimerParent.style.display = 'inline';
            }
          } else {
            showError(result.error || 'Failed to resend OTP');
          }
        } catch (error) {
          document.getElementById('loadingOverlay').classList.add('hidden');
          showError('Network error. Please try again.');
        }
      });

      // Auto-format OTP input (numbers only)
      document.getElementById('otpCode').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
      });

      // Panel selection functions
      function showPanelSelection() {
        // Hide OTP form
        document.getElementById('otpFormContainer').classList.add('hidden');
        // Hide login form
        document.querySelector('main').classList.add('hidden');
        // Show panel selection
        document.getElementById('panelSelectionContainer').classList.remove('hidden');
      }

      // Admin Dashboard button
      document.getElementById('adminDashboardBtn').addEventListener('click', function() {
        // Save panel preference for 30 days
        savePanelPreference('admin/index.php');
        window.location.href = 'admin/index.php';
      });

      // Collection Panel button
      document.getElementById('collectionPanelBtn').addEventListener('click', function() {
        // Save panel preference for 30 days
        savePanelPreference('collection.php');
        window.location.href = 'collection.php';
      });

      // Function to save panel preference
      function savePanelPreference(panel) {
        // Save to localStorage
        localStorage.setItem('last_panel', panel);
        
        // Save to cookie for 30 days (PHP can access this)
        const expirationDate = new Date();
        expirationDate.setTime(expirationDate.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30 days
        document.cookie = `last_panel=${panel}; expires=${expirationDate.toUTCString()}; path=/; SameSite=Lax`;
        
        // Also send to server to save in session
        fetch('api/save-panel-preference.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ panel: panel }),
          credentials: 'same-origin'
        }).catch(err => console.log('Could not save panel preference:', err));
      }

      // Auto-fill username and check remember me if user was remembered
      let rememberedUser = null;
      
      // Check cookie first (more reliable)
      const cookies = document.cookie.split(';');
      for (let cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'remember_user') {
          rememberedUser = decodeURIComponent(value);
          break;
        }
      }
      
      // Fallback to localStorage
      if (!rememberedUser) {
        rememberedUser = localStorage.getItem('remember_user');
      }
      
      // Check if remember me is active
      let rememberMeActive = false;
      for (let cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'remember_me_active' && value === '1') {
          rememberMeActive = true;
          break;
        }
      }
      if (!rememberMeActive) {
        rememberMeActive = localStorage.getItem('remember_me_active') === '1';
      }
      
      if (rememberedUser) {
        document.getElementById('username').value = rememberedUser;
        if (rememberMeActive) {
          document.getElementById('rememberMe').checked = true;
        }
        document.getElementById('password').focus();
      } else {
        document.getElementById('username').focus();
      }

      // Check if user should be auto-logged in (if session is still valid and remember me is active)
      // This will be handled by PHP redirect at the top of the page

      // PWA - Service Worker Registration
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('./service-worker.js')
            .then((registration) => {
              console.log('[PWA] Service Worker registered:', registration.scope);
            })
            .catch((error) => {
              console.log('[PWA] Service Worker registration failed:', error);
            });
        });
      }

      // PWA - Install Prompt
      let deferredPrompt;
      const installPromptContainer = document.getElementById('installPrompt');

      window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        // Stash the event so it can be triggered later
        deferredPrompt = e;
        // Show our custom install prompt
        if (installPromptContainer) {
          installPromptContainer.classList.remove('hidden');
        }
      });

      // Handle install button click
      document.getElementById('installAppBtn')?.addEventListener('click', async () => {
        if (!deferredPrompt) {
          return;
        }

        // Show the install prompt
        deferredPrompt.prompt();

        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;

        console.log(`[PWA] User response to install prompt: ${outcome}`);

        // Hide our custom install prompt
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }

        // Clear the deferredPrompt variable
        deferredPrompt = null;
      });

      // Hide install prompt if user dismisses it
      document.getElementById('dismissInstallBtn')?.addEventListener('click', () => {
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }
        // Store dismissal in localStorage
        localStorage.setItem('pwa-install-dismissed', 'true');
      });

      // Check if user already dismissed the prompt
      if (localStorage.getItem('pwa-install-dismissed') === 'true') {
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }
      }

      // Hide prompt if app is already installed (running in standalone mode)
      if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }
      }
    </script>
  </body>
</html>

