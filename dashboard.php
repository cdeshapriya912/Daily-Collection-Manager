<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Dashboard - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Daily Collection Manager - Dashboard">
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
            <img src="img/package.png" alt="Logo" class="w-12 h-12 object-contain">
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Welcome!</h1>
          <p class="text-white/80 text-lg">Choose where you want to go</p>
        </div>

        <!-- Dashboard Buttons -->
        <div class="space-y-4">
          <!-- Admin Button -->
          <a 
            href="admin/index.php"
            class="block bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all transform hover:scale-105"
          >
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                <span class="material-icons text-green-600 text-3xl">admin_panel_settings</span>
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800 mb-1">Admin Panel</h3>
                <p class="text-gray-600 text-sm">Manage system settings and configurations</p>
              </div>
              <span class="material-icons text-gray-400">arrow_forward</span>
            </div>
          </a>

          <!-- Collection Button -->
          <a 
            href="collection.php"
            class="block bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all transform hover:scale-105"
          >
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-green-bright/20 rounded-full flex items-center justify-center">
                <span class="material-icons text-green-600 text-3xl">collections_bookmark</span>
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800 mb-1">Collection</h3>
                <p class="text-gray-600 text-sm">Collect payments from customers</p>
              </div>
              <span class="material-icons text-gray-400">arrow_forward</span>
            </div>
          </a>
        </div>

        <!-- Logout Button -->
        <div class="mt-8 text-center">
          <a 
            href="login.php"
            class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm font-medium"
          >
            <span class="material-icons text-sm">logout</span>
            Logout
          </a>
        </div>
      </div>
    </main>

    <script>
      // Check if OTP is verified
      <?php
      session_start();
      if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
        echo "window.location.href = 'login.php';";
      }
      ?>
    </script>
  </body>
</html>

