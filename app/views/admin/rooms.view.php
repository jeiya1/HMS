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
  <?php include '../app/views/layouts/header.php'; ?>
  <?php include '../app/views/layouts/sidebar.php'; ?>
  <?php include '../app/views/layouts/topbar.php'; ?>

  <!-- Main Content -->
  <main class="ml-[480px] pt-[50px] px-6">

    <h2 class="text-5xl font-extrabold mt-10 mb-6">
      Room Management
    </h2>

    <button id="add-room-btn" class="bg-green-500 text-white px-6 py-2 rounded-3xl">
      ADD ROOM
    </button>

    <?php include 'components/add_room_modal.php'; ?>

    <div class="grid grid-cols-3 gap-6 mt-10">

      <?php foreach ($rooms as $room): ?>

        <?php include 'components/room_card.php'; ?>

      <?php endforeach; ?>

    </div>

  </main>
</body>

</html>