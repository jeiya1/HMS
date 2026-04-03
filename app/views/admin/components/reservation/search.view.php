<div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-1/2 h-40">
    <h3 class="text-lg font-bold mb-4">Search & Filter Reservations</h3>
    <div class="flex space-x-4">
        <input type="text" id="searchGuest" placeholder="Guest Name" class="border border-gray-300 p-2 rounded w-1/4">
        <input type="date" id="searchDate" class="border border-gray-300 p-2 rounded flex-1">
        <select id="searchStatus" class="border border-gray-300 p-2 rounded flex-1">
            <option value="">All</option>
            <option>Pending</option>
            <option>Confirmed</option>
            <option>Cancelled</option>
        </select>
        <button onclick="applyFilter()" class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer">Apply Filter</button>
        <button onclick="resetFilter()" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">Reset Filter</button>
    </div>
</div>