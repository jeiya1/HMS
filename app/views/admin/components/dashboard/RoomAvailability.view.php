<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
    <h3 class="text-lg font-bold mb-4 text-gray-800 text-center">Room Availability</h3>

    <div class="grid grid-cols-4 gap-6 text-center">
        <div class="bg-gray-50 p-4 border border-gray-300 shadow-xs rounded-lg">
            <p class="text-sm text-gray-500">Occupied</p>
            <p class="text-2xl font-bold text-gray-800"><?= $occupiedRooms ?></p>
        </div>

        <div class="bg-gray-50 p-4 border border-gray-300 shadow-xs rounded-lg">
            <p class="text-sm text-gray-500">Reserved</p>
            <p class="text-2xl font-bold text-gray-800"><?= $reservedRooms ?></p>
        </div>

        <div class="bg-green-500 p-4 rounded-lg shadow-xs">
            <p class="text-sm text-white">Available</p>
            <p class="text-2xl font-bold text-white"><?= $availableRooms ?></p>
        </div>

        <div class="bg-red-500 p-4 rounded-lg shadow-xs">
            <p class="text-sm text-white">Not Ready</p>
            <p class="text-2xl font-bold text-white"><?= $maintenanceRooms ?></p>
        </div>

    </div>
</div>