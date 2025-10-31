<?php
  $pageTitle = isset($pageTitle) ? $pageTitle : 'Daily Collection';
  $pageDescription = isset($pageDescription) ? $pageDescription : 'Daily Collection Manager';
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#10b981">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="manifest" href="manifest.webmanifest?v=10">
<link rel="apple-touch-icon" href="img/package.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="icon" href="img/package.png" type="image/png">
<meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="assets/css/common.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="assets/css/components.css?v=<?php echo time(); ?>">
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
          lg: "0.75rem",
        },
      },
    },
  };
</script>

