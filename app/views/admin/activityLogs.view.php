<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lance Hotel Admin</title>
  <link href="/css/output.css" rel="stylesheet">
  <link href="/css/admin/admin.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-stone-200 font-roboto flex flex-col min-h-screen">
  <!-- Sidebar -->
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>

  <!-- Top Bar -->
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <?php include "../app/views/layouts/header.php"; ?>
    <?php include "../app/views/layouts/sidebar.php"; ?>

    <main class="flex-1 ml-64 mt-8 p-6 space-y-10">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-24">

    <?php include "../app/views/components/admin_logs_table.php"; ?>

    <?php include "../app/views/components/user_logs_table.php"; ?>

    </div>

    </main>

    <script src="/public/js/logs.js"></script>
    <script src="/public/js/settings.js"></script>

    </body>

</html>