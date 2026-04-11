<!-- Quick Statistics -->
<div id="statistics" class="page active">
    <div class="flex justify-between gap-3">

        <div class="bg-white p-4 shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-gray-500 text-center">Total Bookings</p>
            <p class="text-2xl font-bold text-center"><?= $newBookings ?></p>
        </div>

        <div class="bg-white p-4 shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-gray-500 text-center">Total Check-ins</p>
            <p class="text-2xl font-bold text-center"><?= $checkIns ?></p>
        </div>

        <div class="bg-white p-4 shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-gray-500 text-center">Total Check-outs</p>
            <p class="text-2xl font-bold text-center"><?= $checkOuts ?></p>
        </div>

        <div class="bg-gray-700 p-4 shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-white text-center">Total Revenue</p>
            <p class="text-2xl text-white font-bold text-center">₱<?= number_format($revenueTotal, 2) ?></p>
        </div>

    </div>
</div>