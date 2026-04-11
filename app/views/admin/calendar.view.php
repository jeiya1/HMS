<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendar</title>
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
    <div class="flex gap-4 items-start">

      <!-- Calendar Card -->
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm flex-1">

        <!-- Legend -->
        <div class="flex items-center gap-4 mb-4 flex-wrap">
          <span class="text-sm font-semibold text-gray-600">Legend:</span>
          <span class="flex items-center gap-1.5 text-xs text-gray-600">
            <span class="inline-block w-3 h-3 rounded-sm bg-yellow-400"></span> Pending
          </span>
          <span class="flex items-center gap-1.5 text-xs text-gray-600">
            <span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span> Confirmed
          </span>
          <span class="flex items-center gap-1.5 text-xs text-gray-600">
            <span class="inline-block w-3 h-3 rounded-sm bg-blue-400"></span> Checked In
          </span>
          <span class="flex items-center gap-1.5 text-xs text-gray-600">
            <span class="inline-block w-3 h-3 rounded-sm bg-gray-400"></span> Checked Out
          </span>
        </div>

        <div id="calendar"></div>
      </div>

      <!-- Upcoming Check-ins Panel -->
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-68 flex-shrink-0">
        <h3 class="text-sm font-bold text-gray-700 border-b border-gray-200 pb-2 mb-3">Upcoming Check-ins</h3>
        <div class="flex flex-col gap-2 overflow-y-auto max-h-[580px]">
          <?php
            $today    = date('Y-m-d');
            $upcoming = array_filter($events, fn($e) => $e['CheckInDate'] >= $today);
            usort($upcoming, fn($a, $b) => strcmp($a['CheckInDate'], $b['CheckInDate']));
            $upcoming = array_slice($upcoming, 0, 15);

            if (empty($upcoming)):
          ?>
            <p class="text-xs text-gray-400">No upcoming check-ins.</p>
          <?php else: foreach ($upcoming as $ev):
            $sc = match(strtolower($ev['ReservationStatus'])) {
              'pending'    => 'border border-yellow-400 bg-yellow-200 text-yellow-800',
              'confirmed'  => 'border border-green-400 bg-green-200 text-green-800',
              'checked_in' => 'border border-blue-400 bg-blue-200 text-blue-800',
              default      => 'border border-gray-400 bg-gray-200 text-gray-800',
            };
          ?>
            <div class="border border-gray-200 rounded p-2.5 text-xs space-y-0.5 cursor-pointer hover:bg-gray-50 upcoming-item"
              data-token="<?= htmlspecialchars($ev['BookingToken']) ?>">
              <div class="font-semibold text-gray-800">
                <?= htmlspecialchars($ev['FirstName'] . ' ' . $ev['LastName']) ?>
              </div>
              <div class="text-gray-500">
                Room <?= htmlspecialchars($ev['RoomNumber']) ?> · <?= htmlspecialchars($ev['RoomTypeName']) ?>
              </div>
              <div class="text-gray-500">
                <?= $ev['CheckInDate'] ?> → <?= $ev['CheckOutDate'] ?>
              </div>
              <span class="inline-block mt-0.5 px-2 py-0.5 rounded-sm text-xs <?= $sc ?>">
                <?= ucfirst($ev['ReservationStatus']) ?>
              </span>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

    </div>
  </main>

  <!-- Overlay -->
  <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>

  <!-- Event Detail Modal -->
  <div id="eventModal" class="fixed inset-0 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-[520px] p-6 relative max-h-[85vh] overflow-y-auto">
      <button id="closeEventModal"
        class="absolute top-2 right-3 text-gray-500 hover:text-gray-900 text-2xl leading-none cursor-pointer">&times;</button>
      <h2 class="text-xl font-bold mb-4">Reservation Details</h2>
      <div id="eventModalContent" class="space-y-3 text-sm"></div>
    </div>
  </div>

  <script>
    // ── Build events array from PHP ───────────────────────────
    const rawEvents = <?php echo json_encode($events); ?>;

    const colorMap = {
      pending:     '#facc15',
      confirmed:   '#22c55e',
      checked_in:  '#60a5fa',
      checked_out: '#9ca3af',
    };

    // Each ReservationRoom becomes one calendar event
    const calendarEvents = rawEvents.map(ev => ({
      title: `${ev.FirstName} ${ev.LastName} — Rm ${ev.RoomNumber}`,
      start: ev.CheckInDate,
      // FullCalendar end is exclusive, so checkout date shows correctly
      end:   ev.CheckOutDate,
      backgroundColor: colorMap[ev.ReservationStatus] ?? '#9ca3af',
      borderColor:     'transparent',
      textColor:       '#1f2937',
      extendedProps:   ev,
    }));

    // ── Init FullCalendar ─────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
      const calEl = document.getElementById('calendar');

      const calendar = new FullCalendar.Calendar(calEl, {
        initialView:  'dayGridMonth',
        headerToolbar: {
          left:   'prev,next today',
          center: 'title',
          right:  'dayGridMonth,dayGridWeek',
        },
        events:      calendarEvents,
        displayEventTime: false,
        eventClick: function (info) {
          openEventModal(info.event.extendedProps);
        },
        height: 'auto',
      });

      calendar.render();
    });

    // ── Open modal ────────────────────────────────────────────
    function openEventModal(ev) {
      const payColor = {
        pending:   'border border-yellow-400 bg-yellow-200 text-yellow-800',
        completed: 'border border-green-400 bg-green-200 text-green-800',
        failed:    'border border-red-400 bg-red-200 text-red-800',
        refunded:  'border border-gray-400 bg-gray-200 text-gray-800',
      };
      const resColor = {
        pending:     'border border-yellow-400 bg-yellow-200 text-yellow-800',
        confirmed:   'border border-green-400 bg-green-200 text-green-800',
        checked_in:  'border border-blue-400 bg-blue-200 text-blue-800',
        checked_out: 'border border-gray-400 bg-gray-200 text-gray-800',
      };

      const pSt  = (ev.PaymentStatus     ?? '').toLowerCase();
      const rSt  = (ev.ReservationStatus ?? '').toLowerCase();
      const pCls = payColor[pSt] ?? 'border border-gray-400 bg-gray-100 text-gray-800';
      const rCls = resColor[rSt] ?? 'border border-gray-400 bg-gray-100 text-gray-800';

      $('#eventModalContent').html(`
        <div class="space-y-1">
          <p><strong>Booking Code:</strong> ${ev.BookingToken}</p>
          <p><strong>Guest:</strong> ${ev.FirstName} ${ev.LastName}</p>
          <p><strong>Email:</strong> ${ev.Email}</p>
          <p><strong>Phone:</strong> ${ev.PhoneContact ?? '—'}</p>
        </div>
        <hr class="border-gray-200">
        <div class="space-y-1">
          <p><strong>Room:</strong> #${ev.RoomNumber} — ${ev.RoomTypeName}</p>
          <p><strong>Check-in:</strong> ${ev.CheckInDate} <span class="text-gray-400">(12:00 PM)</span></p>
          <p><strong>Check-out:</strong> ${ev.CheckOutDate} <span class="text-gray-400">(11:00 AM)</span></p>
          <p><strong>Guests:</strong> ${ev.NumAdults} Adult(s), ${ev.NumChildren} Child(ren)</p>
          <p><strong>Base Price:</strong> ₱${parseFloat(ev.BasePrice).toLocaleString('en-PH', {minimumFractionDigits:2})}</p>
        </div>
        <hr class="border-gray-200">
        <div class="space-y-1">
          <p><strong>Payment Amount:</strong> ₱${parseFloat(ev.Amount ?? 0).toLocaleString('en-PH', {minimumFractionDigits:2})} (${ev.MethodName ?? '—'})</p>
          <p><strong>Payment Status:</strong>
            <span class="px-2 py-0.5 rounded-sm text-xs ${pCls}">${pSt.charAt(0).toUpperCase() + pSt.slice(1)}</span>
          </p>
          <p><strong>Reservation Status:</strong>
            <span class="px-2 py-0.5 rounded-sm text-xs ${rCls}">${rSt.charAt(0).toUpperCase() + rSt.slice(1)}</span>
          </p>
        </div>
      `);

      $('#overlay, #eventModal').removeClass('hidden');
    }

    $('#closeEventModal, #overlay').on('click', function () {
      $('#overlay, #eventModal').addClass('hidden');
    });

    // ── Upcoming panel: click to open modal ───────────────────
    $(document).on('click', '.upcoming-item', function () {
      const token = $(this).data('token');
      const ev    = rawEvents.find(e => e.BookingToken === token);
      if (ev) openEventModal(ev);
    });
  </script>
</body>
</html>