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

  <!-- Main Content -->
  <?php include "../app/views/layouts/header.php"; ?>
  <?php include "../app/views/layouts/sidebar.php"; ?>

  <main class="flex-1 ml-64 mt-10 p-6 space-y-10">

    <div class="flex flex-col gap-6 md:flex-row">

      <?php include "../app/views/components/calendar_widget.php"; ?>

      <?php include "../app/views/components/event_panel.php"; ?>

    </div>

  </main>

  <script>

    const events = <?= json_encode($events); ?>;

  </script>

  <script src="/public/js/calendar.js"></script>

</body>

</html>