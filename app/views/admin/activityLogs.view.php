<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Logs</title>
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
      <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full">

        <!-- Header Row -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-lg font-semibold text-gray-700">Activity Logs</h2>

            <!-- Filter: Operation -->
            <select id="filterOperation"
              class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-400">
              <option value="">All Operations</option>
              <option value="INSERT">INSERT</option>
              <option value="UPDATE">UPDATE</option>
              <option value="DELETE">DELETE</option>
            </select>

            <!-- Filter: Table -->
            <select id="filterTable"
              class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-400">
              <option value="">All Tables</option>
              <option value="Guests">Guests</option>
              <option value="Users">Users</option>
              <option value="Roles">Roles</option>
              <option value="Rooms">Rooms</option>
              <option value="RoomTypes">RoomTypes</option>
              <option value="Floors">Floors</option>
              <option value="Reservations">Reservations</option>
              <option value="ReservationRooms">ReservationRooms</option>
              <option value="Payments">Payments</option>
              <option value="CartRooms">CartRooms</option>
            </select>
          </div>

          <button id="clearLogsBtn"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded cursor-pointer">
            Clear All Logs
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full border border-gray-300 text-sm">
            <thead>
              <tr class="bg-gray-200">
                <th class="border border-gray-300 p-2 text-center">Log ID</th>
                <th class="border border-gray-300 p-2 text-center">Table</th>
                <th class="border border-gray-300 p-2 text-center">Operation</th>
                <th class="border border-gray-300 p-2 text-center">Record ID</th>
                <th class="border border-gray-300 p-2 text-center">Timestamp</th>
                <th class="border border-gray-300 p-2 text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="logsTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
          <p id="noLogsMsg" class="text-center text-gray-400 py-6 hidden">No logs found.</p>
        </div>

      </div>
    </div>
  </main>

  <!-- Overlay -->
  <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>

  <!-- Log Detail Modal -->
  <div id="logModal" class="fixed inset-0 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-2/3 max-w-3xl p-6 relative max-h-[85vh] overflow-y-auto">
      <button id="closeLogModal"
        class="absolute top-2 right-4 text-gray-500 hover:text-gray-900 text-2xl cursor-pointer">&times;</button>
      <h2 class="text-xl font-bold mb-4">Log Details</h2>

      <div class="space-y-2 text-sm mb-4">
        <p><strong>Log ID:</strong> <span id="modalLogID"></span></p>
        <p><strong>Table:</strong> <span id="modalTableName"></span></p>
        <p><strong>Operation:</strong> <span id="modalOperation"></span></p>
        <p><strong>Record ID:</strong> <span id="modalRecordID"></span></p>
        <p><strong>Timestamp:</strong> <span id="modalTimestamp"></span></p>
      </div>

      <!-- Old Data -->
      <div id="oldDataSection" class="hidden mb-4">
        <h3 class="font-semibold text-gray-600 mb-1">Old Data</h3>
        <pre id="modalOldData"
          class="bg-gray-100 border border-gray-300 rounded p-3 text-xs overflow-x-auto whitespace-pre-wrap"></pre>
      </div>

      <!-- New Data -->
      <div id="newDataSection" class="hidden">
        <h3 class="font-semibold text-gray-600 mb-1">New Data</h3>
        <pre id="modalNewData"
          class="bg-gray-100 border border-gray-300 rounded p-3 text-xs overflow-x-auto whitespace-pre-wrap"></pre>
      </div>
    </div>
  </div>

  <!-- Confirm Modal (reused pattern from payments) -->
  <div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative z-10">
      <h2 class="text-lg font-semibold mb-3">Confirm Action</h2>
      <p id="confirmMessage" class="text-sm text-gray-600 mb-5">Are you sure you want to proceed?</p>
      <div class="flex justify-end space-x-2">
        <button id="cancelConfirm"
          class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400 cursor-pointer">Cancel</button>
        <button id="okConfirm"
          class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 cursor-pointer">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    // ─── State ───────────────────────────────────────────────────────────────
    let allLogs = [];
    let pendingConfirmAction = null;

    // ─── Confirm Modal ────────────────────────────────────────────────────────
    function openConfirmModal(message, action) {
      $('#confirmMessage').text(message);
      pendingConfirmAction = action;
      $('#confirmModal').removeClass('hidden').addClass('flex');
    }

    function closeConfirmModal() {
      $('#confirmModal').addClass('hidden').removeClass('flex');
      pendingConfirmAction = null;
    }

    $('#cancelConfirm').on('click', closeConfirmModal);

    $('#okConfirm').on('click', function () {
      if (typeof pendingConfirmAction === 'function') pendingConfirmAction();
      closeConfirmModal();
    });

    // ─── Operation badge colours ──────────────────────────────────────────────
    function operationBadge(op) {
      const colours = {
        'INSERT': 'bg-green-100 border border-green-400 text-green-800',
        'UPDATE': 'bg-yellow-100 border border-yellow-400 text-yellow-800',
        'DELETE': 'bg-red-100 border border-red-400 text-red-800',
      };
      const cls = colours[op] ?? 'bg-gray-100 border border-gray-400 text-gray-800';
      return `<span class="px-2 py-0.5 rounded-sm text-xs font-medium ${cls}">${op}</span>`;
    }

    // ─── Render table ─────────────────────────────────────────────────────────
    function renderLogs(logs) {
      const tbody = $('#logsTableBody');
      tbody.empty();

      if (!logs.length) {
        $('#noLogsMsg').removeClass('hidden');
        return;
      }

      $('#noLogsMsg').addClass('hidden');

      logs.forEach(log => {
        const row = `
          <tr class="border-b hover:bg-gray-50">
            <td class="p-2 text-center">${log.LogID}</td>
            <td class="p-2 text-center font-mono text-xs">${log.TableName}</td>
            <td class="p-2 text-center">${operationBadge(log.OperationType)}</td>
            <td class="p-2 text-center">${log.RecordID ?? '—'}</td>
            <td class="p-2 text-center">${log.CreatedAt}</td>
            <td class="p-2 text-center space-x-1">
              <button
                class="view-log bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs cursor-pointer"
                data-id="${log.LogID}">
                View
              </button>
              <button
                class="delete-log bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs cursor-pointer"
                data-id="${log.LogID}">
                Delete
              </button>
            </td>
          </tr>`;
        tbody.append(row);
      });
    }

    // ─── Apply filters ────────────────────────────────────────────────────────
    function applyFilters() {
      const op = $('#filterOperation').val();
      const table = $('#filterTable').val();

      const filtered = allLogs.filter(log => {
        return (!op || log.OperationType === op)
          && (!table || log.TableName === table);
      });

      renderLogs(filtered);
    }

    $('#filterOperation, #filterTable').on('change', applyFilters);

    // ─── Fetch all logs ───────────────────────────────────────────────────────
    function loadLogs() {
      $.getJSON('/admin/getLogs', function (data) {
        allLogs = data;
        applyFilters();
      }).fail(function () {
        showToast('Failed to load logs', 'error');
      });
    }

    // ─── View log detail ──────────────────────────────────────────────────────
    $(document).on('click', '.view-log', function () {
      const id = $(this).data('id');
      const log = allLogs.find(l => l.LogID == id);
      if (!log) return;

      $('#modalLogID').text(log.LogID);
      $('#modalTableName').text(log.TableName);
      $('#modalOperation').html(operationBadge(log.OperationType));
      $('#modalRecordID').text(log.RecordID ?? '—');
      $('#modalTimestamp').text(log.CreatedAt);

      // Old Data
      if (log.OldData) {
        try {
          const pretty = JSON.stringify(JSON.parse(log.OldData), null, 2);
          $('#modalOldData').text(pretty);
        } catch {
          $('#modalOldData').text(log.OldData);
        }
        $('#oldDataSection').removeClass('hidden');
      } else {
        $('#oldDataSection').addClass('hidden');
      }

      // New Data
      if (log.NewData) {
        try {
          const pretty = JSON.stringify(JSON.parse(log.NewData), null, 2);
          $('#modalNewData').text(pretty);
        } catch {
          $('#modalNewData').text(log.NewData);
        }
        $('#newDataSection').removeClass('hidden');
      } else {
        $('#newDataSection').addClass('hidden');
      }

      $('#overlay, #logModal').removeClass('hidden');
    });

    // Close log modal
    $('#closeLogModal, #overlay').on('click', function () {
      $('#overlay, #logModal').addClass('hidden');
    });

    // ─── Delete single log ────────────────────────────────────────────────────
    $(document).on('click', '.delete-log', function () {
      const id = $(this).data('id');

      openConfirmModal('Delete this log entry?', function () {
        $.post('/admin/deleteLog', { logID: id }, function (res) {
          if (res.success) {
            showToast('Log deleted', 'success');
            loadLogs();
          } else {
            showToast(res.message || 'Failed to delete log', 'error');
          }
        }, 'json');
      });
    });

    // ─── Clear all logs ───────────────────────────────────────────────────────
    $('#clearLogsBtn').on('click', function () {
      openConfirmModal('Clear ALL logs? This cannot be undone.', function () {
        $.post('/admin/clearLogs', function (res) {
          if (res.success) {
            showToast('All logs cleared', 'success');
            loadLogs();
          } else {
            showToast(res.message || 'Failed to clear logs', 'error');
          }
        }, 'json');
      });
    });

    // ─── Auto-refresh every 15 s ──────────────────────────────────────────────
    loadLogs();
    setInterval(loadLogs, 15000);
  </script>

</body>

</html>