<!-- Quick Statistics -->
<div id="statistics" class="page active">
    <div class="flex justify-between gap-3">
        <div class="bg-white p-4  shadow-sm  border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-gray-500 text-center">New Bookings</p>
            <p class="text-2xl font-bold text-center"><?= $newBookings ?> <span class="text-green-500 text-sm text-center">↑ 8.7%</span>
            </p>
        </div>
        <div class="bg-white p-4 shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-gray-500 text-center">Check-In</p>
            <p class="text-2xl font-bold text-center"><?= $checkIns ?> <span class="text-green-500 text-sm text-center">↑
                    3.56%</span></p>
        </div>
        <div class="bg-white p-4  shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-gray-500 text-center">Check-Out</p>
            <p class="text-2xl font-bold text-center"><?= $checkOuts ?> <span class="text-red-500 text-sm text-center">↓ 1.06%</span>
            </p>
        </div>
        <div class="bg-gray-700 p-4  shadow-sm border border-gray-300 rounded-lg w-1/4">
            <p class="text-sm text-white text-center">Total Revenue</p>
            <p class="text-2xl text-white font-bold text-center"><?= number_format($revenueTotal,2) ?>
                <span class="text-green-500 text-sm text-center">↑ 5.7%</span>
            </p>
        </div>
    </div>
</div>