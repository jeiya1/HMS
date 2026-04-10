<?php
// Set admin username
$admin_username = "Admin";
?>
<!-- Header -->
<header class="flex justify-end items-center bg-gray-300 p-3 pr-5 shadow-sm fixed w-full z-40">
    <!-- Right side: Profile + Settings -->
    <div class="flex items-center gap-4">
        <!-- Profile -->
        <div class="flex items-center gap-3">
            <img src="/assets/images/man-face-black-linear-cartoon-icon-user-isolated-white-background-ai-generated_1095381-16809.avif"
                alt="Profile" class="w-10 h-10 rounded-full">
            <div class="flex flex-col text-right">
                <p class="text-md font-bold text-gray-900"><?php echo $admin_username; ?></p>
                <p class="text-sm text-gray-500">Admin</p>
            </div>
        </div>

        <!-- Settings Button -->
        <button id="settings-btn"
            class="flex items-center justify-center p-2 rounded hover:rounded-full hover:bg-gray-200 transition cursor-pointer">
            <ion-icon name="settings-outline" class="text-lg text-gray-900"></ion-icon>
        </button>
    </div>
</header>

<!-- Overlay -->
<div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-40"></div>

<!-- Settings Popup -->
<div id="settings-popup" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
            w-[500px] bg-white rounded-lg shadow-sm p-6 hidden 
            transition-transform duration-300 scale-0 z-50 border border-gray-300">

    <div class="flex justify-between">
        <!-- Header -->
        <div class="flex items-center mb-6">
            <div
                class="w-12 h-12 flex items-center justify-center border border-gray-300-2 border border-gray-300-gray-800 rounded-full">
                <ion-icon name="person-circle-outline" class="text-lg text-gray-800"></ion-icon>
            </div>
            <span class="bl-4 text-xl font-semibold text-gray-800 ml-4"><?php echo $admin_username; ?></span>
        </div>
        <div class="flex gap-2">
            <button id="logout-btn"
                class="bg-red-500 text-white w-9 h-9 rounded-full hover:bg-red-400/70 flex items-center justify-center cursor-pointer">
                <ion-icon name="log-out-outline" class="text-xl"></ion-icon>
            </button>
            <!-- Close Button -->
            <button id="close-settings" class="w-9 h-9 flex items-center justify-center 
               bg-gray-800 text-white rounded-full hover:bg-gray-600 transition cursor-pointer">
                ✕
            </button>

        </div>
    </div>

    <!-- Change Username -->
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1 flex items-center gap-2">
            <ion-icon name="person-circle-outline"></ion-icon> Change Username
        </label>
        <input type="text"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <hr class="my-4">

    <!-- Current Password -->
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1 flex items-center gap-2">
            <ion-icon name="lock-closed-outline"></ion-icon> Current Password
        </label>
        <input type="password"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Change Password -->
    <div class="mb-6">
        <label class="block text-gray-700 font-medium mb-1 flex items-center gap-2">
            <ion-icon name="lock-open-outline"></ion-icon> Change Password
        </label>
        <input type="password"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Confirm Button -->
    <button id="confirm-btn"
        class="bg-green-500 text-white font-semibold py-2 rounded w-full hover:bg-green-400 transition cursor-pointer">
        Confirm
    </button>

</div>

<!-- Logout Overlay -->
<div id="logout-overlay" class="fixed inset-0 bg-black/70 hidden z-50"></div>

<!-- Logout Popup -->
<div id="logout-popup" class="fixed inset-0 hidden z-60 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-100 text-center border border-gray-300">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Confirm Logout</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to log out?</p>
        <div class="flex gap-3">
            <button id="cancel-logout" class="flex-1 bg-gray-400 text-white py-2 rounded hover:bg-gray-500 transition cursor-pointer">
                Cancel
            </button>
            <button id="confirm-logout" class="flex-1 bg-red-600 text-white py-2 rounded hover:bg-red-700 transition cursor-pointer">
                Logout
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="/admin/js/settings.js"></script>
<script src="/admin/js/logout.js"></script>