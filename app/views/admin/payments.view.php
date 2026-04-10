<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments</title>
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
  <main class="ml-64 mt-15.5 p-4">
    <div class="flex justify-center">
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full h-1/2">
        <table class="w-full border border-gray-300">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-300 p-2 text-center">Booking Code</th>
              <th class="border border-gray-300 p-2 text-center">Guest Name</th>
              <th class="border border-gray-300 p-2 text-center">Rooms</th>
              <th class="border border-gray-300 p-2 text-center">Payment Method</th>
              <th class="border border-gray-300 p-2 text-center">Payment Amount</th>
              <th class="border border-gray-300 p-2 text-center">Payment Status</th>
              <th class="border border-gray-300 p-2 text-center">Transaction Reference</th>
              <th class="border border-gray-300 p-2 text-center">Payment Date & Time</th>
              <th class="border border-gray-300 p-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="reservationTable">
            <?php foreach ($payments as $payment): ?>
              <tr class="border-b">
                <td class="p-2 text-center">
                  <?= htmlspecialchars($payment['BookingToken']) ?>
                </td>
                <td class="p-2 text-center">
                  <?= htmlspecialchars($payment['FirstName'] . ' ' . $payment['LastName']) ?>
                </td>
                <td class="p-2 text-center">
                  <?= htmlspecialchars($payment['TotalGuests']) ?>
                </td>
                <td class="p-2 text-center">
                  <?= htmlspecialchars($payment['MethodName']) ?>
                </td>
                <td class="p-2 text-center">₱
                  <?= number_format($payment['Amount'], 2) ?>
                </td>
                <td class="p-2 text-center">
                  <?php
                  $status = strtolower($payment['PaymentStatus']);
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
                  <?= htmlspecialchars($payment['TransactionReference']) ?>
                </td>
                <td class="p-2 text-center">
                  <?= date('Y-m-d H:i', strtotime($payment['PaymentDate'])) ?>
                </td>
                <td class="p-2 text-center space-x-2">
                  <button class="bg-blue-500 text-white px-2 py-1 rounded view-payment cursor-pointer"
                    data-id="<?= $payment['PaymentID'] ?>">View</button>

                  <?php if (strtolower($payment['PaymentStatus']) === 'pending'): ?>
                    <button class="bg-green-500 text-white px-2 py-1 rounded confirm-payment cursor-pointer"
                      data-booking="<?= $payment['BookingToken'] ?>">Confirm</button>
                  <?php endif; ?>

                  <?php if (strtolower($payment['PaymentStatus']) === 'completed'): ?>
                    <button class="bg-red-500 text-white px-2 py-1 rounded refund-payment cursor-pointer"
                      data-booking="<?= $payment['BookingToken'] ?>">Refund</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Overlay for blurred background -->
  <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>

  <!-- Modal -->
  <div id="paymentModal" class="fixed inset-0 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-1/2 p-6 relative">
      <button id="closeModal"
        class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 cursor-pointer">&times;</button>
      <h2 class="text-xl font-bold mb-4">Payment Details</h2>
      <div class="space-y-2">
        <p><strong>Booking Code:</strong> <span id="modalBookingToken"></span></p>
        <p><strong>Guest Name:</strong> <span id="modalGuestName"></span></p>
        <p><strong>Rooms:</strong> <span id="modalRooms"></span></p>
        <p><strong>Payment Method:</strong> <span id="modalMethod"></span></p>
        <p><strong>Amount:</strong> ₱<span id="modalAmount"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <p><strong>Transaction Ref:</strong> <span id="modalTransaction"></span></p>
        <p><strong>Payment Date:</strong> <span id="modalDate"></span></p>
        <div class="mt-4">
          <h3 class="font-semibold mb-2">Room Details</h3>
          <div id="modalRoomsList" class="space-y-2 text-sm"></div>
        </div>
      </div>
    </div>
  </div>
  <div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative z-10">
      <h2 class="text-lg font-semibold mb-3">Confirm Action</h2>

      <p id="confirmMessage" class="text-sm text-gray-600 mb-5">
        Are you sure you want to proceed?
      </p>

      <div class="flex justify-end space-x-2">
        <button id="cancelConfirm" class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400 cursor-pointer">
          Cancel
        </button>

        <button id="okConfirm" class="px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 cursor-pointer">
          Confirm
        </button>
      </div>
    </div>
  </div>
  <script>
    $('.view-payment').on('click', function () {
      let row = $(this).closest('tr');

      $('#modalBookingToken').text(row.find('td:nth-child(1)').text());
      $('#modalGuestName').text(row.find('td:nth-child(2)').text());
      $('#modalMethod').text(row.find('td:nth-child(4)').text());
      $('#modalAmount').text(row.find('td:nth-child(5)').text().replace('₱', '').trim());
      $('#modalStatus').text(row.find('td:nth-child(6)').text().trim());
      $('#modalTransaction').text(row.find('td:nth-child(7)').text());
      $('#modalDate').text(row.find('td:nth-child(8)').text());

      let paymentID = $(this).data('id');

      // Load rooms
      $.get('/admin/getPaymentRooms', { paymentID }, function (rooms) {
        let html = '';

        if (!rooms.length) {
          html = '<p class="text-gray-500">No rooms found</p>';
        } else {
          rooms.forEach(room => {
            let totalGuests = parseInt(room.NumAdults) + parseInt(room.NumChildren);

            html += `
          <div class="border rounded p-3">
            <div class="font-semibold">
              Room #${room.RoomNumber} - ${room.RoomTypeName}
            </div>
            <div>Occupancy: ${room.NumAdults} Adult(s), ${room.NumChildren} Child(ren)</div>
            <div>Total Guests: ${totalGuests}</div>
            <div>Check-in: ${room.CheckInDate} (12:00 PM)</div>
            <div>Check-out: ${room.CheckOutDate} (11:00 AM)</div>
          </div>
        `;
          });
        }

        $('#modalRoomsList').html(html);
      }, 'json');

      $('#overlay, #paymentModal').removeClass('hidden');
    });

    // Close modal
    $('#closeModal, #overlay').on('click', function () {
      $('#overlay, #paymentModal').addClass('hidden');
    });

    // Confirm payment button (in table row)
    $(document).on('click', '.confirm-payment', function () {
      const bookingCode = $(this).data('booking');

      openConfirmModal('Confirm this payment?', function () {
        $.post('/admin/confirmPayment', { bookingCode }, function (res) {
          if (res.success) {
            showToast('Payment confirmed successfully', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed to confirm payment', 'error');
          }
        }, 'json');
      });
    });

    // Refund payment button (in table row)
    $(document).on('click', '.refund-payment', function () {
      let bookingCode = $(this).data('booking');

      openConfirmModal('Refund this payment?', function () {
        $.post('/admin/refundPayment', { bookingCode }, function (res) {
          if (res.success) {
            showToast('Payment refunded successfully', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed to refund payment', 'error');
          }
        }, 'json');
      });
    });
  </script>
  <script>
    let pendingConfirmAction = null;

    function openConfirmModal(message, action) {
      $('#confirmMessage').text(message);
      pendingConfirmAction = action;

      $('#confirmModal')
        .removeClass('hidden')
        .addClass('flex');
    }

    function closeConfirmModal() {
      $('#confirmModal')
        .addClass('hidden')
        .removeClass('flex');

      pendingConfirmAction = null;
    }

    $('#cancelConfirm').on('click', function () {
      closeConfirmModal();
    });

    $('#okConfirm').on('click', function () {
      if (typeof pendingConfirmAction === 'function') {
        pendingConfirmAction();
      }
      closeConfirmModal();
    });
  </script>
  <script>
    function reloadPayments() {
      $.getJSON('/admin/getPayments', function (data) {
        let html = '';

        data.forEach(p => {
          html += `
        <tr class="border-b">
          <td class="p-2 text-center">${p.BookingToken}</td>
          <td class="p-2 text-center">${p.FirstName} ${p.LastName}</td>
          <td class="p-2 text-center">${p.TotalGuests}</td>
          <td class="p-2 text-center">${p.MethodName}</td>
          <td class="p-2 text-center">₱${p.Amount}</td>
          <td class="p-2 text-center">${p.PaymentStatus}</td>
          <td class="p-2 text-center">${p.TransactionReference}</td>
          <td class="p-2 text-center">${p.PaymentDate}</td>
        </tr>
      `;
        });

        $('#reservationTable').html(html);
      });
    }

    setInterval(reloadPayments, 10000);
  </script>
</body>

</html>