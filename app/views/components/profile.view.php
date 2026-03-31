<?php if ($logged_in): ?>
    <div class="relative group inline-block">

        <!-- Trigger -->
        <button class="flex items-center text-white focus:outline-none cursor-pointer">
            <span
                class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
                <?php echo htmlspecialchars($_SESSION['logged_in_user_name'] ?? 'Profile'); ?>
            </span>

            <!-- SVG include -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/assets/icons/dropdown.svg'; ?>
        </button>

        <!-- Dropdown -->
        <div
            class="absolute right-0 mt-2 w-40 bg-white text-black rounded shadow-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200 z-50">

            <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">
                Profile
            </a>

            <!-- TODO: Add Bookings -->
            <a href="/bookings" class="block px-4 py-2 hover:bg-gray-100">
                Bookings
            </a>

            <a href="/logout" id="logoutBtn" class="block px-4 py-2 hover:bg-gray-100">
                Logout
            </a>

        </div>
    </div>

<?php else: ?>
    <!-- Sign In button for guests -->
    <a href="/registration">
        <button class="rounded-sm hover:bg-[#3F321F] shadow-2xl py-2 px-4 bg-[#C39C4D] transition-colors cursor-pointer group">
            <p
                class="transition-all duration-300 text-white group-hover:text-white group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)]">
                Sign In
            </p>
        </button>
    </a>
<?php endif; ?>
<script src="/js/auth.js"></script>