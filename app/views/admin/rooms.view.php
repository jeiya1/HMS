<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rooms</title>
  <link rel="icon" type="image/x-icon" href="/admin/assets/icons/favicon.svg">
  <link href="/admin/css/output.css" rel="stylesheet">
  <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="bg-stone-200 font-roboto flex flex-col min-h-screen">
  <?php include_once __DIR__ . '/components/toast.view.php'; ?>
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <main class="ml-64 mt-15.5 p-4">
    <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-full">

      <!-- Page Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Room Management</h1>
      </div>

      <!-- Tabs -->
      <div class="flex border-b border-gray-300 mb-6">
        <button id="tab-types"
          class="tab-btn px-6 py-2 text-sm font-medium border-b-2 border-blue-500 text-blue-600 cursor-pointer">
          Room Types
        </button>
        <button id="tab-rooms"
          class="tab-btn px-6 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 cursor-pointer">
          Individual Rooms
        </button>
      </div>

      <!-- ═══════════════════════════════════════════════
           TAB 1: ROOM TYPES
           ═══════════════════════════════════════════════ -->
      <div id="panel-types">
        <div class="flex justify-end mb-4">
          <button id="add-type-btn"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded cursor-pointer">
            + Add Room Type
          </button>
        </div>

        <table class="w-full border border-gray-300 text-sm">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-300 p-2 text-center">ID</th>
              <th class="border border-gray-300 p-2 text-center">Name</th>
              <th class="border border-gray-300 p-2 text-center">Base Price</th>
              <th class="border border-gray-300 p-2 text-center">Bed Type</th>
              <th class="border border-gray-300 p-2 text-center">Beds</th>
              <th class="border border-gray-300 p-2 text-center">Max Occupancy</th>
              <th class="border border-gray-300 p-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($roomTypes as $type): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="p-2 text-center"><?= $type['RoomTypeID'] ?></td>
              <td class="p-2 text-center font-medium"><?= htmlspecialchars($type['RoomTypeName']) ?></td>
              <td class="p-2 text-center">₱<?= number_format($type['BasePrice'], 2) ?></td>
              <td class="p-2 text-center"><?= htmlspecialchars($type['BedName'] ?? '—') ?></td>
              <td class="p-2 text-center"><?= $type['BedCount'] ?></td>
              <td class="p-2 text-center"><?= $type['MaxOccupancy'] ?></td>
              <td class="p-2 text-center space-x-1">
                <button class="edit-type-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs cursor-pointer"
                  data-id="<?= $type['RoomTypeID'] ?>"
                  data-name="<?= htmlspecialchars($type['RoomTypeName']) ?>"
                  data-price="<?= $type['BasePrice'] ?>"
                  data-bedtype="<?= $type['BedTypeID'] ?>"
                  data-bedcount="<?= $type['BedCount'] ?>"
                  data-occupancy="<?= $type['MaxOccupancy'] ?>">
                  Edit
                </button>
                <button class="delete-type-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs cursor-pointer"
                  data-id="<?= $type['RoomTypeID'] ?>"
                  data-name="<?= htmlspecialchars($type['RoomTypeName']) ?>">
                  Delete
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ═══════════════════════════════════════════════
           TAB 2: INDIVIDUAL ROOMS
           ═══════════════════════════════════════════════ -->
      <div id="panel-rooms" class="hidden">
        <div class="flex items-center gap-3 mb-4 justify-between">
          <div class="flex gap-3">
            <!-- Filter by status -->
            <select id="filterStatus"
              class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none">
              <option value="">All Statuses</option>
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="maintenance">Maintenance</option>
            </select>
            <!-- Filter by floor -->
            <select id="filterFloor"
              class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none">
              <option value="">All Floors</option>
              <?php foreach ($floors as $floor): ?>
              <option value="<?= $floor['FloorNumber'] ?>">Floor <?= $floor['FloorNumber'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button id="add-room-btn"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded cursor-pointer">
            + Add Room
          </button>
        </div>

        <table class="w-full border border-gray-300 text-sm">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-300 p-2 text-center">Room No.</th>
              <th class="border border-gray-300 p-2 text-center">Floor</th>
              <th class="border border-gray-300 p-2 text-center">Type</th>
              <th class="border border-gray-300 p-2 text-center">Bed</th>
              <th class="border border-gray-300 p-2 text-center">Max Occupancy</th>
              <th class="border border-gray-300 p-2 text-center">Base Price</th>
              <th class="border border-gray-300 p-2 text-center">Status</th>
              <th class="border border-gray-300 p-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="roomsTableBody">
            <?php foreach ($rooms as $room): ?>
            <?php
              $status = strtolower($room['Status']);
              $statusColor = match($status) {
                'available'   => 'bg-green-100 border border-green-400 text-green-800',
                'occupied'    => 'bg-blue-100 border border-blue-400 text-blue-800',
                'maintenance' => 'bg-yellow-100 border border-yellow-400 text-yellow-800',
                default       => 'bg-gray-100 border border-gray-400 text-gray-800',
              };
            ?>
            <tr class="border-b hover:bg-gray-50" data-status="<?= $status ?>" data-floor="<?= $room['FloorNumber'] ?>">
              <td class="p-2 text-center font-mono font-semibold"><?= htmlspecialchars($room['RoomNumber']) ?></td>
              <td class="p-2 text-center"><?= $room['FloorNumber'] ?></td>
              <td class="p-2 text-center"><?= htmlspecialchars($room['RoomTypeName']) ?></td>
              <td class="p-2 text-center"><?= htmlspecialchars($room['BedName'] ?? '—') ?> × <?= $room['BedCount'] ?></td>
              <td class="p-2 text-center"><?= $room['MaxOccupancy'] ?></td>
              <td class="p-2 text-center">₱<?= number_format($room['BasePrice'], 2) ?></td>
              <td class="p-2 text-center">
                <span class="px-2 py-0.5 rounded-sm text-xs font-medium <?= $statusColor ?>">
                  <?= ucfirst($status) ?>
                </span>
              </td>
              <td class="p-2 text-center space-x-1">
                <?php if ($status !== 'maintenance'): ?>
                <button class="set-maintenance-btn bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs cursor-pointer"
                  data-id="<?= $room['RoomID'] ?>" data-number="<?= $room['RoomNumber'] ?>">
                  Maintenance
                </button>
                <?php else: ?>
                <button class="set-available-btn bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs cursor-pointer"
                  data-id="<?= $room['RoomID'] ?>" data-number="<?= $room['RoomNumber'] ?>">
                  Set Available
                </button>
                <?php endif; ?>
                <button class="delete-room-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs cursor-pointer"
                  data-id="<?= $room['RoomID'] ?>" data-number="<?= $room['RoomNumber'] ?>">
                  Delete
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!-- ═══════════════════════════════════════════════════════
       OVERLAY
       ═══════════════════════════════════════════════════════ -->
  <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>

  <!-- ═══════════════════════════════════════════════════════
       MODAL: Add / Edit Room Type
       ═══════════════════════════════════════════════════════ -->
  <div id="typeModal" class="fixed inset-0 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-[480px] p-6 relative">
      <button id="closeTypeModal" class="absolute top-2 right-4 text-gray-500 hover:text-gray-900 text-2xl cursor-pointer">&times;</button>
      <h2 id="typeModalTitle" class="text-xl font-bold mb-5">Add Room Type</h2>

      <div class="space-y-4">
        <input type="hidden" id="typeID">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Room Type Name</label>
          <input id="typeName" type="text" placeholder="e.g. Deluxe Single"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Base Price (₱)</label>
          <input id="typePrice" type="number" min="0" step="0.01" placeholder="0.00"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bed Type</label>
          <select id="typeBedType" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none">
            <?php foreach ($bedTypes as $bed): ?>
            <option value="<?= $bed['BedTypeID'] ?>"><?= htmlspecialchars($bed['BedName']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bed Count</label>
            <input id="typeBedCount" type="number" min="1" value="1"
              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Max Occupancy</label>
            <input id="typeOccupancy" type="number" min="1" value="2"
              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
          </div>
        </div>

        <button id="saveTypeBtn"
          class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-sm cursor-pointer mt-2">
          Save
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       MODAL: Add Room
       ═══════════════════════════════════════════════════════ -->
  <div id="roomModal" class="fixed inset-0 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-[420px] p-6 relative">
      <button id="closeRoomModal" class="absolute top-2 right-4 text-gray-500 hover:text-gray-900 text-2xl cursor-pointer">&times;</button>
      <h2 class="text-xl font-bold mb-5">Add Room</h2>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
          <input id="roomNumber" type="text" placeholder="e.g. 205"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
          <select id="roomFloor" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none">
            <?php foreach ($floors as $floor): ?>
            <option value="<?= $floor['FloorID'] ?>">Floor <?= $floor['FloorNumber'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
          <select id="roomType" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none">
            <?php foreach ($roomTypes as $type): ?>
            <option value="<?= $type['RoomTypeID'] ?>"><?= htmlspecialchars($type['RoomTypeName']) ?> — ₱<?= number_format($type['BasePrice'], 2) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button id="saveRoomBtn"
          class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-sm cursor-pointer mt-2">
          Add Room
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       CONFIRM MODAL
       ═══════════════════════════════════════════════════════ -->
  <div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative z-10">
      <h2 class="text-lg font-semibold mb-3">Confirm Action</h2>
      <p id="confirmMessage" class="text-sm text-gray-600 mb-5">Are you sure?</p>
      <div class="flex justify-end space-x-2">
        <button id="cancelConfirm" class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400 cursor-pointer">Cancel</button>
        <button id="okConfirm" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 cursor-pointer">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    // ─── Tabs ─────────────────────────────────────────────────────────────────
    $('#tab-types').on('click', function () {
      $('#panel-types').removeClass('hidden');
      $('#panel-rooms').addClass('hidden');
      $('#tab-types').addClass('border-blue-500 text-blue-600').removeClass('border-transparent text-gray-500');
      $('#tab-rooms').addClass('border-transparent text-gray-500').removeClass('border-blue-500 text-blue-600');
    });

    $('#tab-rooms').on('click', function () {
      $('#panel-rooms').removeClass('hidden');
      $('#panel-types').addClass('hidden');
      $('#tab-rooms').addClass('border-blue-500 text-blue-600').removeClass('border-transparent text-gray-500');
      $('#tab-types').addClass('border-transparent text-gray-500').removeClass('border-blue-500 text-blue-600');
    });

    // ─── Room filters ─────────────────────────────────────────────────────────
    function applyRoomFilters() {
      const status = $('#filterStatus').val();
      const floor  = $('#filterFloor').val();

      $('#roomsTableBody tr').each(function () {
        const rowStatus = $(this).data('status');
        const rowFloor  = String($(this).data('floor'));
        const matchStatus = !status || rowStatus === status;
        const matchFloor  = !floor  || rowFloor  === floor;
        $(this).toggle(matchStatus && matchFloor);
      });
    }

    $('#filterStatus, #filterFloor').on('change', applyRoomFilters);

    // ─── Confirm Modal ────────────────────────────────────────────────────────
    let pendingConfirmAction = null;

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

    // ─── Generic modal helpers ────────────────────────────────────────────────
    function openModal(id) {
      $('#overlay, #' + id).removeClass('hidden');
      if (id !== 'logModal') $('#' + id).addClass('flex');
    }

    function closeModal(id) {
      $('#overlay, #' + id).addClass('hidden').removeClass('flex');
    }

    $('#overlay').on('click', function () {
      closeModal('typeModal');
      closeModal('roomModal');
    });

    $('#closeTypeModal').on('click', () => closeModal('typeModal'));
    $('#closeRoomModal').on('click', () => closeModal('roomModal'));

    // ─── ROOM TYPES: Add ─────────────────────────────────────────────────────
    $('#add-type-btn').on('click', function () {
      $('#typeModalTitle').text('Add Room Type');
      $('#typeID').val('');
      $('#typeName').val('');
      $('#typePrice').val('');
      $('#typeBedCount').val(1);
      $('#typeOccupancy').val(2);
      openModal('typeModal');
    });

    // ─── ROOM TYPES: Edit ────────────────────────────────────────────────────
    $(document).on('click', '.edit-type-btn', function () {
      $('#typeModalTitle').text('Edit Room Type');
      $('#typeID').val($(this).data('id'));
      $('#typeName').val($(this).data('name'));
      $('#typePrice').val($(this).data('price'));
      $('#typeBedType').val($(this).data('bedtype'));
      $('#typeBedCount').val($(this).data('bedcount'));
      $('#typeOccupancy').val($(this).data('occupancy'));
      openModal('typeModal');
    });

    // ─── ROOM TYPES: Save (Add or Edit) ──────────────────────────────────────
    $('#saveTypeBtn').on('click', function () {
      const id         = $('#typeID').val();
      const name       = $('#typeName').val().trim();
      const price      = $('#typePrice').val().trim();
      const bedTypeID  = $('#typeBedType').val();
      const bedCount   = $('#typeBedCount').val();
      const occupancy  = $('#typeOccupancy').val();

      if (!name || !price) {
        showToast('Please fill in all fields', 'error');
        return;
      }

      const endpoint = id ? '/admin/updateRoomType' : '/admin/addRoomType';
      const payload  = { name, price, bedTypeID, bedCount, occupancy };
      if (id) payload.roomTypeID = id;

      $.post(endpoint, payload, function (res) {
        if (res.success) {
          showToast(res.message || 'Saved', 'success');
          setTimeout(() => location.reload(), 800);
        } else {
          showToast(res.message || 'Failed', 'error');
        }
      }, 'json');
    });

    // ─── ROOM TYPES: Delete ───────────────────────────────────────────────────
    $(document).on('click', '.delete-type-btn', function () {
      const id   = $(this).data('id');
      const name = $(this).data('name');

      openConfirmModal(`Delete room type "${name}"? This cannot be undone.`, function () {
        $.post('/admin/deleteRoomType', { roomTypeID: id }, function (res) {
          if (res.success) {
            showToast('Room type deleted', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed to delete', 'error');
          }
        }, 'json');
      });
    });

    // ─── ROOMS: Add ───────────────────────────────────────────────────────────
    $('#add-room-btn').on('click', function () {
      $('#roomNumber').val('');
      openModal('roomModal');
    });

    $('#saveRoomBtn').on('click', function () {
      const roomNumber = $('#roomNumber').val().trim();
      const floorID    = $('#roomFloor').val();
      const roomTypeID = $('#roomType').val();

      if (!roomNumber) {
        showToast('Please enter a room number', 'error');
        return;
      }

      $.post('/admin/addRoom', { roomNumber, floorID, roomTypeID }, function (res) {
        if (res.success) {
          showToast('Room added', 'success');
          setTimeout(() => location.reload(), 800);
        } else {
          showToast(res.message || 'Failed to add room', 'error');
        }
      }, 'json');
    });

    // ─── ROOMS: Set Maintenance ───────────────────────────────────────────────
    $(document).on('click', '.set-maintenance-btn', function () {
      const id     = $(this).data('id');
      const number = $(this).data('number');

      openConfirmModal(`Set Room ${number} to Maintenance?`, function () {
        $.post('/admin/updateRoomStatus', { roomID: id, status: 'maintenance' }, function (res) {
          if (res.success) {
            showToast('Room set to maintenance', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed', 'error');
          }
        }, 'json');
      });
    });

    // ─── ROOMS: Set Available ─────────────────────────────────────────────────
    $(document).on('click', '.set-available-btn', function () {
      const id     = $(this).data('id');
      const number = $(this).data('number');

      openConfirmModal(`Set Room ${number} back to Available?`, function () {
        $.post('/admin/updateRoomStatus', { roomID: id, status: 'available' }, function (res) {
          if (res.success) {
            showToast('Room set to available', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed', 'error');
          }
        }, 'json');
      });
    });

    // ─── ROOMS: Delete ────────────────────────────────────────────────────────
    $(document).on('click', '.delete-room-btn', function () {
      const id     = $(this).data('id');
      const number = $(this).data('number');

      openConfirmModal(`Delete Room ${number}? This cannot be undone.`, function () {
        $.post('/admin/deleteRoom', { roomID: id }, function (res) {
          if (res.success) {
            showToast('Room deleted', 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(res.message || 'Failed to delete room', 'error');
          }
        }, 'json');
      });
    });
  </script>
</body>
</html>