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
  <?php include_once __DIR__ . '/components/toast.view.php'; ?>
  <!-- Sidebar -->
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>

  <!-- Top Bar -->
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <main class="ml-64 mt-15.5 p-4 h-screen">
    <div class="mb-8">
      <!-- Top Section: Live Feed + Search & Filter -->
      <!-- Search & Filter Reservations -->
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full h-40">
        <h3 class="text-lg font-bold mb-4">Search & Filter Reservations</h3>
        <div class="flex space-x-4">
          <input type="text" id="searchGuest" placeholder="Guest Name" class="border border-gray-300 p-2 rounded w-1/4">
          <input type="date" id="searchDate" class="border border-gray-300 p-2 rounded flex-1">
          <select id="searchStatus" class="border border-gray-300 p-2 rounded flex-1">
            <option value="">All</option>
            <option>Pending</option>
            <option>Confirmed</option>
            <option>Cancelled</option>
          </select>
          <button onclick="applyFilter()" class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer">Apply
            Filter</button>
          <button onclick="resetFilter()" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">Reset
            Filter</button>
        </div>
      </div>
    </div>
    <!-- Bottom Section: Reservation List -->
    <div class="flex justify-center">
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full h-1/2">
        <h3 class="text-lg font-bold mb-4">Reservation List</h3>
        <table class="w-full border border-gray-300">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-300 p-2 text-center">Booking Code</th>
              <th class="border border-gray-300 p-2 text-center">Guest Name</th>
              <th class="border border-gray-300 p-2 text-center">Check-in</th>
              <th class="border border-gray-300 p-2 text-center">Check-out</th>
              <th class="border border-gray-300 p-2 text-center">Number of Rooms</th>
              <th class="border border-gray-300 p-2 text-center">Total Room Amount</th>
              <th class="border border-gray-300 p-2 text-center">Payment Amount (Method)</th>
              <th class="border border-gray-300 p-2 text-center">Payment Status</th>
              <th class="border border-gray-300 p-2 text-center">Reservation Status</th>
              <th class="border border-gray-300 p-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="reservationTable">
            <?php foreach ($reservations as $res): ?>

              <?php
              $guestName = $res['GuestFirstName'] . ' ' . $res['GuestLastName'];

              $roomCount = count($res['Rooms']);

              $checkIn = $res['Rooms'][0]['CheckInDate'] ?? '-';
              $checkOut = $res['Rooms'][0]['CheckOutDate'] ?? '-';

              $totalAmount = 0;
              foreach ($res['Rooms'] as $room) {
                $totalAmount += $room['BasePrice'];
              }
              ?>

              <tr class="border-b">
                <td class="p-2 text-center"><?= $res['BookingToken'] ?></td>

                <td class="p-2 text-center"><?= $guestName ?></td>

                <td class="p-2 text-center"><?= $checkIn ?></td>

                <td class="p-2 text-center"><?= $checkOut ?></td>

                <td class="p-2 text-center"><?= $roomCount ?></td>

                <td class="p-2 text-center">₱<?= number_format($totalAmount, 2) ?></td>

                <td class="p-2 text-center">
                  ₱<?= number_format($res['PaymentAmount'] ?? 0, 2) ?>
                  (<?= $res['PaymentMethod'] ?? '-' ?>)
                </td>

                <td class="p-2 text-center">
                  <?php
                  $status = strtolower($res['PaymentStatus'] ?? '');
                  $statusColor = match ($status) {
                    'pending' => 'border border-yellow-400 bg-yellow-200 text-yellow-800',
                    'completed' => 'border border-green-400 bg-green-200 text-green-800',
                    'failed' => 'border border-red-400 bg-red-200 text-red-800',
                    'refunded' => 'border border-gray-400 bg-gray-200 text-gray-800',
                    default => 'border border-gray-400 bg-gray-100 text-gray-800',
                  };
                  ?>
                  <span class="px-2 py-1 rounded-sm <?= $statusColor ?>">
                    <?= ucfirst($status) ?>
                  </span>
                </td>
                <td class="p-2 text-center">
                  <?php
                  $resStatus = strtolower($res['ReservationStatus'] ?? '');
                  $resColor = match ($resStatus) {
                    'pending' => 'border border-yellow-400 bg-yellow-200 text-yellow-800',
                    'confirmed' => 'border border-green-400 bg-green-200 text-green-800',
                    'cancelled' => 'border border-red-400 bg-red-200 text-red-800',
                    default => 'border border-gray-400 bg-gray-100 text-gray-800',
                  };
                  ?>
                  <span class="px-2 py-1 rounded-sm <?= $resColor ?>">
                    <?= ucfirst($resStatus) ?>
                  </span>
                </td>

                <td class="p-2 text-center space-x-2">

                  <button class="bg-blue-500 text-white px-2 py-1 rounded view-reservation cursor-pointer"
                    data-booking="<?= $res['BookingToken'] ?>">
                    View
                  </button>

                  <?php
                  $paymentStatus = trim(strtolower($res['PaymentStatus'] ?? ''));
                  $resStatus = trim(strtolower($res['ReservationStatus'] ?? ''));

                  if ($resStatus === 'pending'):
                    ?>
                    <button class="bg-green-500 text-white px-2 py-1 rounded approve-reservation cursor-pointer"
                      data-booking="<?= $res['BookingToken'] ?>">
                      Approve
                    </button>
                  <?php endif; ?>

                  <button class="bg-red-500 text-white px-2 py-1 rounded cancel-reservation cursor-pointer"
                    data-booking="<?= $res['BookingToken'] ?>">
                    Cancel
                  </button>

                </td>
              </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

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
    <div id="detailsModal" class="fixed inset-0 hidden items-center justify-center z-50">
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
    <div id="cancelModal" class="fixed inset-0 hidden items-center justify-center z-50">
      <!-- backdrop -->
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

      <!-- modal box -->
      <div class="relative bg-white p-6 rounded-lg shadow-lg text-center w-[320px] border border-gray-200">

        <h3 id="confirmTitle" class="text-lg font-bold mb-4">Confirm Action</h3>

        <p id="confirmMessage" class="mb-6 text-gray-600">
          Are you sure you want to proceed?
        </p>

        <div class="flex justify-center space-x-4">
          <button id="confirmCancelBtn" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer">
            Yes
          </button>

          <button id="closeCancelBtn" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">
            No
          </button>
        </div>

      </div>
    </div>
  </main>
  <div id="detailsOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>
  <script>
    // =========================
    // VIEW RESERVATION DETAILS
    // =========================
    $(document).on('click', '.view-reservation', function () {
      const bookingToken = $(this).data('booking');

      $.get('/admin/getReservationDetails', { bookingToken }, function (data) {

        let html = '';

        if (!data || !data.length) {
          html = '<p class="text-sm text-gray-500">No details found</p>';
        } else {
          const guest = data[0];

          html += `
        <div class="text-sm space-y-1">
          <p><strong>Booking Code:</strong> ${guest.BookingToken}</p>
          <p><strong>Guest:</strong> ${guest.FirstName} ${guest.LastName}</p>
          <p><strong>Email:</strong> ${guest.Email}</p>
        </div>
        <hr class="my-3">
        <h4 class="font-semibold text-sm mb-2">Rooms</h4>
      `;

          data.forEach(room => {
            html += `
          <div class="border rounded p-2 mb-2 text-sm">
            <div><strong>Room:</strong> ${room.RoomType} (#${room.RoomNumber})</div>
            <div>Check-in: ${room.CheckInDate}</div>
            <div>Check-out: ${room.CheckOutDate}</div>
            <div>Guests: ${room.NumAdults} Adult(s), ${room.NumChildren} Child(ren)</div>
          </div>
        `;
          });
        }

        $('#detailsContent').html(html);

        // IMPORTANT: show modal properly (like payments page)
        $('#detailsModal')
          .removeClass('hidden')
          .addClass('flex');
      }, 'json');
    });


    // =========================
    // CLOSE MODAL
    // =========================
    function closeModal() {
      $('#detailsModal')
        .addClass('hidden')
        .removeClass('flex');
    }

    // click outside modal closes
    $(document).on('click', '#detailsModal', function (e) {
      if (e.target.id === 'detailsModal') {
        closeModal();
      }
    });


    // =========================
    // APPROVE RESERVATION
    // =========================
    $(document).on('click', '.approve-reservation', function () {
      const bookingToken = $(this).data('booking');

      openConfirmModal(
        'Approve Reservation',
        'Are you sure you want to approve this reservation?',
        function () {
          $.post('/admin/approveReservation', { bookingToken }, function (res) {
            if (res.success) {
              showToast('Reservation approved successfully', 'success');
              setTimeout(() => location.reload(), 800);
            } else {
              showToast(res.message || 'Failed to approve reservation', 'error');
            }
          }, 'json');
        }
      );
    });


    // =========================
    // CANCEL RESERVATION
    // =========================
    $(document).on('click', '.cancel-reservation', function () {
      const bookingToken = $(this).data('booking');

      openConfirmModal(
        'Cancel Reservation',
        'Are you sure you want to cancel this reservation?',
        function () {
          $.post('/admin/cancelReservationAdmin', { bookingToken }, function (res) {
            if (res.success) {
              showToast('Reservation cancelled successfully', 'success');
              setTimeout(() => location.reload(), 800);
            } else {
              showToast(res.message || 'Failed to cancel reservation', 'error');
            }
          }, 'json');
        }
      );
    });


    // =========================
    // SIMPLE CONFIRM MODAL (REUSED STYLE LIKE PAYMENTS PAGE)
    // =========================
    let pendingAction = null;

    function openConfirmModal(title, message, action) {
      pendingAction = action;

      $('#confirmTitle').text(title);
      $('#confirmMessage').text(message);

      $('#cancelModal')
        .removeClass('hidden')
        .addClass('flex');
    }

    function closeConfirmModal() {
      $('#cancelModal')
        .addClass('hidden')
        .removeClass('flex');

      pendingAction = null;
    }

    $(document).on('click', '#confirmCancelBtn', function () {
      if (typeof pendingAction === 'function') pendingAction();
      closeConfirmModal();
    });

    $(document).on('click', '#closeCancelBtn', function () {
      closeConfirmModal();
    });
  </script>
</body>

</html>