<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations</title>
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
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <main class="ml-64 mt-15.5 p-4">

    <!-- Search & Filter -->
    <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full mb-4">
      <h3 class="text-lg font-bold mb-4">Search & Filter Reservations</h3>
      <div class="flex flex-wrap gap-3">
        <input type="text" id="searchGuest" placeholder="Guest Name"
          class="border border-gray-300 p-2 rounded w-48">
        <input type="date" id="searchDate"
          class="border border-gray-300 p-2 rounded">
        <select id="searchStatus" class="border border-gray-300 p-2 rounded">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <button onclick="applyFilter()"
          class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer">
          Apply Filter
        </button>
        <button onclick="resetFilter()"
          class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">
          Reset Filter
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="flex justify-center">
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full">
        <table class="w-full border border-gray-300">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-300 p-2 text-center">Booking Code</th>
              <th class="border border-gray-300 p-2 text-center">Guest Name</th>
              <th class="border border-gray-300 p-2 text-center">Check-in</th>
              <th class="border border-gray-300 p-2 text-center">Check-out</th>
              <th class="border border-gray-300 p-2 text-center">Rooms</th>
              <th class="border border-gray-300 p-2 text-center">Room Total</th>
              <th class="border border-gray-300 p-2 text-center">Payment Amount (Method)</th>
              <th class="border border-gray-300 p-2 text-center">Payment Status</th>
              <th class="border border-gray-300 p-2 text-center">Reservation Status</th>
              <th class="border border-gray-300 p-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="reservationTable">

            <?php foreach ($reservations as $res): ?>
              <?php
                $guestName   = $res['GuestFirstName'] . ' ' . $res['GuestLastName'];
                $roomCount   = count($res['Rooms']);
                $checkIn     = $res['Rooms'][0]['CheckInDate']  ?? '-';
                $checkOut    = $res['Rooms'][0]['CheckOutDate'] ?? '-';
                $totalAmount = array_sum(array_column($res['Rooms'], 'BasePrice'));
                $payStatus   = strtolower(trim($res['PaymentStatus']     ?? ''));
                $resStatus   = strtolower(trim($res['ReservationStatus'] ?? ''));

                $payColor = match($payStatus) {
                  'pending'   => 'border border-yellow-400 bg-yellow-200 text-yellow-800',
                  'completed' => 'border border-green-400 bg-green-200 text-green-800',
                  'failed'    => 'border border-red-400 bg-red-200 text-red-800',
                  'refunded'  => 'border border-gray-400 bg-gray-200 text-gray-800',
                  default     => 'border border-gray-400 bg-gray-100 text-gray-800',
                };
                $resColor = match($resStatus) {
                  'pending'   => 'border border-yellow-400 bg-yellow-200 text-yellow-800',
                  'confirmed' => 'border border-green-400 bg-green-200 text-green-800',
                  'cancelled' => 'border border-red-400 bg-red-200 text-red-800',
                  default     => 'border border-gray-400 bg-gray-100 text-gray-800',
                };
              ?>
              <tr class="border-b"
                data-guest="<?= strtolower(htmlspecialchars($guestName)) ?>"
                data-checkin="<?= $checkIn ?>"
                data-resstatus="<?= $resStatus ?>">

                <td class="p-2 text-center"><?= htmlspecialchars($res['BookingToken']) ?></td>
                <td class="p-2 text-center"><?= htmlspecialchars($guestName) ?></td>
                <td class="p-2 text-center"><?= htmlspecialchars($checkIn) ?></td>
                <td class="p-2 text-center"><?= htmlspecialchars($checkOut) ?></td>
                <td class="p-2 text-center"><?= $roomCount ?></td>
                <td class="p-2 text-center">₱<?= number_format($totalAmount, 2) ?></td>
                <td class="p-2 text-center">
                  ₱<?= number_format($res['PaymentAmount'] ?? 0, 2) ?>
                  (<?= htmlspecialchars($res['PaymentMethod'] ?? '-') ?>)
                </td>

                <td class="p-2 text-center">
                  <span class="px-2 py-1 rounded-sm <?= $payColor ?>">
                    <?= ucfirst($payStatus) ?>
                  </span>
                </td>

                <td class="p-2 text-center">
                  <span class="px-2 py-1 rounded-sm <?= $resColor ?>">
                    <?= ucfirst($resStatus) ?>
                  </span>
                </td>

                <td class="p-2 text-center space-x-1">
                  <button class="view-reservation bg-blue-500 text-white px-2 py-1 rounded cursor-pointer"
                    data-booking="<?= $res['BookingToken'] ?>">View</button>

                  <?php if ($resStatus === 'pending'): ?>
                  <button class="approve-reservation bg-green-500 text-white px-2 py-1 rounded cursor-pointer"
                    data-booking="<?= $res['BookingToken'] ?>">Approve</button>
                  <?php endif; ?>

                  <?php if ($resStatus !== 'cancelled'): ?>
                  <button class="cancel-reservation bg-red-500 text-white px-2 py-1 rounded cursor-pointer"
                    data-booking="<?= $res['BookingToken'] ?>">Cancel</button>
                  <?php endif; ?>
                </td>

              </tr>
            <?php endforeach; ?>

            <?php if (empty($reservations)): ?>
            <tr>
              <td colspan="10" class="p-6 text-center text-gray-400">No reservations found.</td>
            </tr>
            <?php endif; ?>

          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Overlay -->
  <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>

  <!-- Details Modal -->
  <div id="detailsModal" class="fixed inset-0 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-[600px] max-h-[80vh] overflow-y-auto p-6 relative">
      <button id="closeDetailsModal"
        class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 cursor-pointer text-xl leading-none">&times;</button>
      <h2 class="text-xl font-bold mb-4">Reservation Details</h2>
      <div id="detailsContent" class="space-y-3 text-sm"></div>
    </div>
  </div>

  <!-- Confirm Modal -->
  <div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative z-10">
      <h2 id="confirmTitle" class="text-lg font-semibold mb-3">Confirm Action</h2>
      <p id="confirmMessage" class="text-sm text-gray-600 mb-5">Are you sure you want to proceed?</p>
      <div class="flex justify-end space-x-2">
        <button id="cancelConfirm"
          class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400 cursor-pointer">Cancel</button>
        <button id="okConfirm"
          class="px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 cursor-pointer">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    // ── Filter ────────────────────────────────────────────────
    function applyFilter() {
      const name   = $('#searchGuest').val().toLowerCase();
      const date   = $('#searchDate').val();
      const status = $('#searchStatus').val().toLowerCase();

      $('#reservationTable tr').each(function () {
        const matchName   = !name   || $(this).data('guest').includes(name);
        const matchDate   = !date   || $(this).data('checkin') === date;
        const matchStatus = !status || $(this).data('resstatus') === status;
        $(this).toggle(matchName && matchDate && matchStatus);
      });
    }

    function resetFilter() {
      $('#searchGuest').val('');
      $('#searchDate').val('');
      $('#searchStatus').val('');
      $('#reservationTable tr').show();
    }

    // ── Details Modal ─────────────────────────────────────────
    $(document).on('click', '.view-reservation', function () {
      const bookingToken = $(this).data('booking');

      $.get('/admin/getReservationDetails', { bookingToken }, function (data) {
        let html = '';

        if (!data || !data.length) {
          html = '<p class="text-gray-500">No details found.</p>';
        } else {
          const guest = data[0];
          html += `
            <div class="space-y-1">
              <p><strong>Booking Code:</strong> ${guest.BookingToken}</p>
              <p><strong>Guest:</strong> ${guest.FirstName} ${guest.LastName}</p>
              <p><strong>Email:</strong> ${guest.Email}</p>
            </div>
            <hr class="my-3">
            <h3 class="font-semibold mb-2">Room Details</h3>
          `;

          data.forEach(room => {
            html += `
              <div class="border rounded p-3">
                <div class="font-semibold">Room #${room.RoomNumber} — ${room.RoomType}</div>
                <div>Check-in: ${room.CheckInDate} (12:00 PM)</div>
                <div>Check-out: ${room.CheckOutDate} (11:00 AM)</div>
                <div>Guests: ${room.NumAdults} Adult(s), ${room.NumChildren} Child(ren)</div>
              </div>
            `;
          });
        }

        $('#detailsContent').html(html);
        $('#overlay, #detailsModal').removeClass('hidden');
      }, 'json');
    });

    $('#closeDetailsModal, #overlay').on('click', function () {
      $('#overlay, #detailsModal').addClass('hidden');
    });

    function closeModal() {
      $('#overlay, #detailsModal').addClass('hidden');
    }

    // ── Confirm Modal ─────────────────────────────────────────
    let pendingAction = null;

    function openConfirmModal(title, message, action) {
      pendingAction = action;
      $('#confirmTitle').text(title);
      $('#confirmMessage').text(message);
      $('#confirmModal').removeClass('hidden').addClass('flex');
    }

    function closeConfirmModal() {
      $('#confirmModal').addClass('hidden').removeClass('flex');
      pendingAction = null;
    }

    $('#cancelConfirm').on('click', closeConfirmModal);
    $('#okConfirm').on('click', function () {
      if (typeof pendingAction === 'function') pendingAction();
      closeConfirmModal();
    });

    // ── Approve ───────────────────────────────────────────────
    $(document).on('click', '.approve-reservation', function () {
      const bookingToken = $(this).data('booking');
      openConfirmModal('Approve Reservation', 'Are you sure you want to approve this reservation?', function () {
        $.post('/admin/approveReservation', { bookingToken }, function (res) {
          if (res.success) {
            showToast('Reservation approved successfully', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed to approve reservation', 'error');
          }
        }, 'json');
      });
    });

    // ── Cancel ────────────────────────────────────────────────
    $(document).on('click', '.cancel-reservation', function () {
      const bookingToken = $(this).data('booking');
      openConfirmModal('Cancel Reservation', 'Are you sure you want to cancel this reservation?', function () {
        $.post('/admin/cancelReservationAdmin', { bookingToken }, function (res) {
          if (res.success) {
            showToast('Reservation cancelled successfully', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed to cancel reservation', 'error');
          }
        }, 'json');
      });
    });
  </script>
</body>
</html>