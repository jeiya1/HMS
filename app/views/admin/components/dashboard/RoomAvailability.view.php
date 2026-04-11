<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Room Availability</h3>
        <span class="text-xs text-gray-400 border border-gray-200 bg-gray-50 px-2 py-1 rounded">
            Today — <?= date('F j, Y') ?>
        </span>
    </div>
    <div class="grid grid-cols-4 gap-6 text-center">

        <div class="bg-gray-50 p-4 border border-gray-300 shadow-xs rounded-lg">
            <p class="text-sm text-gray-500">Occupied</p>
            <p class="text-2xl font-bold text-gray-800"><?= $occupiedRooms ?></p>
        </div>

        <div class="bg-gray-50 p-4 border border-gray-300 shadow-xs rounded-lg">
            <p class="text-sm text-gray-500">Reserved Today</p>
            <p class="text-2xl font-bold text-gray-800"><?= $reservedRooms ?></p>
        </div>

        <div class="bg-green-500 p-4 rounded-lg shadow-xs">
            <p class="text-sm text-white">Available Today</p>
            <p class="text-2xl font-bold text-white"><?= $availableRooms ?></p>
        </div>

        <div class="bg-red-500 p-4 rounded-lg shadow-xs">
            <p class="text-sm text-white">Maintenance</p>
            <p class="text-2xl font-bold text-white"><?= $maintenanceRooms ?></p>
        </div>

    </div>
</div>