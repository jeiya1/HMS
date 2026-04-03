<div class="flex justify-between gap-6">
    <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-1/4">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Reservations (Last 7 Days)</h3>
        <canvas id="reservationChart"></canvas>
    </div>
    <div class="bg-white p-6 border border-gray-300 rounded-lg  shadow-sm w-1/4">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Reservations (This Month)</h3>
        <canvas id="monthChart"></canvas>
    </div>
    <div class="bg-white p-6 border border-gray-300 rounded-lg  shadow-sm w-1/4">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Revenue (Last 6 Months)</h3>
        <canvas id="revenueChart"></canvas>
    </div>

    <?php include_once __DIR__ . '/OverallRating.view.php'; ?>
</div>