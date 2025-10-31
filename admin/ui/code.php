<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#10b981",
            "background-light": "#f7fafc",
            "background-dark": "#1a202c",
            "card-light": "#ffffff",
            "card-dark": "#2d3748",
            "text-light": "#4a5568",
            "text-dark": "#a0aec0",
            "heading-light": "#1a202c",
            "heading-dark": "#f7fafc",
            "border-light": "#e2e8f0",
            "border-dark": "#4a5568",
          },
          fontFamily: {
            sans: ["Inter", "sans-serif"],
          },
          borderRadius: {
            'lg': "0.75rem",
          },
        },
      },
    };
  </script>
<style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark">
<div class="flex h-screen">
<aside class="w-64 bg-card-light dark:bg-card-dark border-r border-border-light dark:border-border-dark flex-shrink-0">
<div class="p-6 flex items-center gap-3">
<div class="bg-primary p-2 rounded-lg">
<span class="material-icons text-white">
            all_inbox
          </span>
</div>
<h1 class="text-xl font-bold text-heading-light dark:text-heading-dark">Daily Collection</h1>
</div>
<nav class="mt-8 px-4">
<ul>
<li>
<a class="flex items-center gap-3 px-4 py-3 bg-primary/10 dark:bg-primary/20 text-primary rounded-lg font-semibold" href="#">
<span class="material-icons">dashboard</span>
              Dashboard
            </a>
</li>
<li class="mt-2">
<a class="flex items-center gap-3 px-4 py-3 text-text-light dark:text-text-dark hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg" href="#">
<span class="material-icons">inventory_2</span>
              Product
            </a>
</li>
<li class="mt-2">
<a class="flex items-center gap-3 px-4 py-3 text-text-light dark:text-text-dark hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg" href="#">
<span class="material-icons">collections_bookmark</span>
              Collection
            </a>
</li>
<li class="mt-2">
<a class="flex items-center gap-3 px-4 py-3 text-text-light dark:text-text-dark hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg" href="#">
<span class="material-icons">people</span>
              User
            </a>
</li>
</ul>
</nav>
</aside>
<div class="flex-1 flex flex-col">
<header class="flex items-center justify-between p-6 border-b border-border-light dark:border-border-dark">
<button class="md:hidden text-text-light dark:text-text-dark">
<span class="material-icons">menu</span>
</button>
<h2 class="text-2xl font-bold text-heading-light dark:text-heading-dark">Dashboard</h2>
<div class="flex items-center gap-4">
<button class="text-text-light dark:text-text-dark">
<span class="material-icons">notifications</span>
</button>
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
<span class="material-icons text-text-light dark:text-text-dark">person</span>
</div>
</div>
</div>
</header>
<main class="flex-1 p-6 lg:p-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<div class="bg-card-light dark:bg-card-dark p-6 rounded-lg border border-border-light dark:border-border-dark col-span-1 md:col-span-2">
<h3 class="text-lg font-semibold text-heading-light dark:text-heading-dark">Overview</h3>
<p class="mt-2 text-text-light dark:text-text-dark">Welcome to the Daily Collection Manager dashboard PWA demo.</p>
</div>
<div class="bg-card-light dark:bg-card-dark p-6 rounded-lg border border-border-light dark:border-border-dark">
<h3 class="text-lg font-semibold text-heading-light dark:text-heading-dark">Quick Stats</h3>
<ul class="mt-4 space-y-3 text-text-light dark:text-text-dark">
<li class="flex justify-between items-center">
<span>Products</span>
<span class="font-semibold text-heading-light dark:text-heading-dark">24</span>
</li>
<li class="flex justify-between items-center">
<span>Collections</span>
<span class="font-semibold text-heading-light dark:text-heading-dark">6</span>
</li>
<li class="flex justify-between items-center">
<span>Users</span>
<span class="font-semibold text-heading-light dark:text-heading-dark">3</span>
</li>
</ul>
</div>
</div>
</main>
</div>
</div>
</body></html>