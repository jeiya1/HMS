<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lance Hotel Admin</title>
  <link rel="icon" type="image/x-icon" href="/admin/assets/icons/favicon.svg">
  <link href="/admin/css/output.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body class="bg-stone-200 font-roboto flex flex-col min-h-screen">
  <!-- Sidebar -->
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>

  <!-- Top Bar -->
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <main class="ml-64 mt-15.5 p-4 h-screen">
    <div class="flex justify-between space-x-8 mb-8">
      <!-- Top Section: Live Feed + Search & Filter -->
      <?php include_once __DIR__ . '/components/reservation/feed.view.php'; ?>
      <!-- Search & Filter Reservations -->
      <?php include_once __DIR__ . '/components/reservation/search.view.php'; ?>
    </div>
    <!-- Bottom Section: Reservation List -->
    <?php include_once __DIR__ . '/components/reservation/list.view.php'; ?>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 hidden flex items-center justify-center backdrop-blur-sm">
      <div class="bg-white p-6 rounded rounded-lg border border-gray-300 shadow-sm-lg w-[800px]">
        <!-- increased width -->
        <h2 class="text-lg font-bold mb-4">Edit Reservation</h2>
        <form id="editForm" class="grid grid-cols-2 gap-4"> <!-- two-column grid -->
          <div>
            <label class="block text-sm font-medium mb-1">Guest Name</label>
            <input type="text" class="border border-gray-300 p-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Room</label>
            <input type="number" class="border border-gray-300 p-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Room Type</label>
            <input type="text" class="border border-gray-300 p-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Guests</label>
            <input type="number" class="border border-gray-300 p-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Check-in</label>
            <input type="date" class="border border-gray-300 p-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Check-out</label>
            <input type="date" class="border border-gray-300 p-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Method</label>
            <select class="border border-gray-300 p-2 w-full">
              <option>Online</option>
              <option>Walk-in</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select class="border border-gray-300 p-2 w-full">
              <option>Pending</option>
              <option>Confirmed</option>
              <option>Cancelled</option>
            </select>
          </div>
        </form>
        <div class="flex justify-end space-x-2 mt-6">
          <button id="confirmEditBtn" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer">Confirm</button>
          <button id="cancelEditBtn" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Container 5: Reservation Details (Modal) -->
    <div id="detailsModal" class="hidden fixed inset-0 z-30">
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

      <!-- Modal Container -->
      <div class="fixed inset-0 flex items-center justify-center">
        <div class="bg-white rounded-lg border border-gray-300 shadow-lg w-[600px] max-h-[80vh] overflow-y-auto p-6">
          <h3 class="text-lg font-bold mb-4 text-center">Reservation Details</h3>
          <div id="detailsContent" class="space-y-2"></div>
          <div class="mt-4 flex justify-center space-x-2">
            <button onclick="closeModal()" class="bg-red-500 text-white px-4 py-2 rounded cursor-pointer">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm hidden">
      <div class="bg-white p-6 rounded-lg shadow-sm-lg text-center w-[300px]">
        <h3 class="text-lg font-bold mb-4">Cancel Reservation?</h3>
        <p class="mb-6">Are you sure you want to cancel this reservation?</p>
        <div class="flex justify-center space-x-4">
          <button id="confirmCancelBtn" class="bg-red-500 text-white px-4 py-2 rounded cursor-pointer">Yes,
            Cancel</button>
          <button id="closeCancelBtn" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">No</button>
        </div>
      </div>
    </div>
  </main>
  <script src="/admin/js/reservation.js"></script>
  <script src="/admin/js/calendar.js"></script>
</body>

</html>