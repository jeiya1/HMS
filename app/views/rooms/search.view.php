<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Rooms</title>
    <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="flex-1 py-10 px-30 flex flex-col gap-5">
        <div class="flex text-black text-base font-normal font-crimson">
            <!-- Home button with SVG -->
            <a href="/home" class="flex items-center border border-neutral-300 px-4 py-1">
                <img src="/assets/icons/home.svg" alt="Home" class="w-4 h-4">
            </a>

            <a href="/search" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Search
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">SEARCH ROOMS</h1>
        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <div
                class="flex-1 bg-linear-to-tl from-[#FCEDB5] to-[#40331F] rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] max-h-86">
                <form method="POST" action="/search">
                    <div class="flex flex-col gap-2 p-5">
                        <!-- Check-in/Check-out -->
                        <div class="relative">
                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/calendar.svg" alt="calendar">
                            </span>
                            <input type="text" name="checkin" id="daterange" placeholder="Check-In — Check-Out" required
                                class="bg-white rounded-sm p-2 pl-9 text-crimson-600 font-crimson border border-gray-300 w-full cursor-pointer"
                                value="<?= isset($_GET['checkin']) ? htmlspecialchars($_GET['checkin']) : '' ?>">
                        </div>

                        <!-- Adults -->
                        <div class="relative">
                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/people.svg" alt="people">
                            </span>
                            <input type="number" id="adults" name="adults" placeholder="No. of Adults" min="0"
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 w-full"
                                value="<?= !empty($_GET['adults']) ? (int) $_GET['adults'] : '' ?>">
                        </div>

                        <!-- Children -->
                        <div class="relative">
                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/people.svg" alt="people">
                            </span>
                            <input type="number" id="children" name="children" placeholder="No. of Children" min="0"
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 w-full"
                                value="<?= !empty($_GET['children']) ? (int) $_GET['children'] : '' ?>">
                        </div>

                        <!-- Room Type Radios -->
                        <div class="flex items-center gap-4 font-crimson">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="radio" name="room" value="single" data-base-price="1800"
                                    class="peer sr-only" <?= (isset($_GET['room']) && $_GET['room'] === 'single') ? 'checked' : '' ?>>
                                <div class="w-5 h-5 border-2 border-white rounded-full shrink-0
                                peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                                <span class="ml-2 text-white">Single</span>
                            </label>

                            <label class="relative flex items-center cursor-pointer">
                                <input type="radio" name="room" value="double" data-base-price="2700"
                                    class="peer sr-only" <?= (isset($_GET['room']) && $_GET['room'] === 'double') ? 'checked' : '' ?>>
                                <div class="w-5 h-5 border-2 border-white rounded-full shrink-0
                                peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                                <span class="ml-2 text-white">Double</span>
                            </label>
                        </div>

                        <!-- Room Type Select -->
                        <select name="room_type"
                            class="border border-white text-white bg-transparent p-2 rounded font-crimson cursor-pointer">
                            <option value="" class="text-black cursor-pointer">Any Type</option>
                            <option value="Standard" class="text-black cursor-pointer" <?= (isset($_GET['room_type']) && $_GET['room_type'] == 'Standard') ? 'selected' : '' ?>>Standard</option>
                            <option value="Deluxe" class="text-black cursor-pointer" <?= (isset($_GET['room_type']) && $_GET['room_type'] == 'Deluxe') ? 'selected' : '' ?>>Deluxe</option>
                            <option value="Suite" class="text-black cursor-pointer" <?= (isset($_GET['room_type']) && $_GET['room_type'] == 'Suite') ? 'selected' : '' ?>>Suite</option>
                        </select>

                        <!-- Clear Filters Button -->
                        <div class="flex justify-end relative z-50">
                            <button type="button" id="clearFilters"
                                class="text-white underline font-roboto text-sm cursor-pointer">
                                Clear Filters
                            </button>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                            class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#714623] p-2.5 cursor-pointer hover:bg-[#654022] transition-colors duration-300 group">
                            <p class="hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)]">
                                SEARCH ROOMS
                            </p>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Results -->
            <div class="flex-3 rounded-[1px] border-[0.30px] border-zinc-500">
                <div class="pt-5 px-5 pb-1 flex items-center gap-3">
                    <label for="sortPrice" class="text-black text-xs font-light font-roboto">Sort by:</label>
                    <select id="sortPrice" class="p-2 rounded border border-gray-300 font-roboto cursor-pointer">
                        <option class="cursor-pointer" value="">-- Select --</option>
                        <option class="cursor-pointer" value="asc">Price: Low → High</option>
                        <option class="cursor-pointer" value="desc">Price: High → Low</option>
                    </select>
                </div>

                <div class="flex flex-col gap-4 p-5">
                    <?php foreach ($rooms as $room): ?>
                        <?php
                        $type = strtolower($room['RoomTypeName']); // e.g., "deluxe room"
                        if (stripos($type, 'standard') !== false) {
                            $roomRoute = 'standard';
                        } elseif (stripos($type, 'deluxe') !== false) {
                            $roomRoute = 'deluxe';
                        } elseif (stripos($type, 'suite') !== false) {
                            $roomRoute = 'suite';
                        } else {
                            $roomRoute = 'standard'; // fallback
                        }
                        ?>
                        <div class="room-item" data-price="<?= htmlspecialchars($room['BasePrice']) ?>"
                            data-room="<?= htmlspecialchars($room['RoomNumber']) ?>">

                            <div class="mx-auto h-0.5 w-full bg-yellow-900/60 rounded-lg mb-7"></div>
                            <div class="flex gap-3 my-2">
                                <div class="flex-1">
                                    <img src="<?= htmlspecialchars($roomPics[$room['RoomTypeName']] ?? '/assets/images/standard.jpg') ?>"
                                        alt="Room Image">
                                </div>

                                <div class="flex-3 flex flex-col gap-3">
                                    <h2 class="font-crimson font-medium text-3xl">
                                        Room
                                        <?= htmlspecialchars($room['RoomNumber']) ?> -
                                        <?= htmlspecialchars($room['RoomTypeName']) ?>
                                    </h2>
                                    <p class="font-roboto">
                                        <?= $roomDescriptions[$room['RoomTypeName']] ?? "A comfortable and well-appointed room designed to meet your needs." ?>
                                    </p>

                                    <div class="flex gap-2 -mt-1">
                                        <img src="/assets/icons/wifi.svg" alt="Wi-Fi Icon" class="w-5 h-5">
                                        <img src="/assets/icons/AC.svg" alt="Air Condition Icon" class="w-5 h-5">
                                    </div>

                                    <div class="flex justify-between mt-3">
                                        <div>
                                            <h3 class="justify-center text-zinc-500 text-xs font-medium font-roboto">MAX NO.
                                                GUESTS</h3>
                                            <p>Max Occupancy:
                                                <?= htmlspecialchars($room['MaxOccupancy']) ?>
                                            </p>
                                        </div>
                                        <p class="font-crimson text-2xl">₱
                                            <?= htmlspecialchars($room['BasePrice']) ?>/Per Night
                                        </p>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 cursor-pointer hover:bg-[#3F321F] transition-colors group">
                                            <a href="/room/<?= $roomRoute ?>?room=<?= $room['RoomNumber'] ?>&checkin=<?= urlencode($_GET['checkin'] ?? '') ?>&checkout=<?= urlencode($_GET['checkout'] ?? '') ?>"
                                                class="transition-all duration-300 text-white group-hover:text-white
                              group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                                                VIEW MORE
                                            </a>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($rooms)): ?>
                        <p class="text-red-500">No rooms match your criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/daterange.js"></script>
    <script src="/js/clearFilter.js"></script>
    <script>
        $(document).ready(function () {
            const $roomContainer = $('.flex.flex-col.gap-4.p-5'); // container holding room items

            $('#sortPrice').on('change', function () {
                const order = $(this).val();
                const $rooms = $roomContainer.children('.room-item');

                if (!order) return; // do nothing if no selection

                const sorted = $rooms.sort(function (a, b) {
                    const priceA = parseFloat($(a).data('price'));
                    const priceB = parseFloat($(b).data('price'));
                    return order === 'asc' ? priceA - priceB : priceB - priceA;
                });

                $roomContainer.html(sorted);
            });
        });
    </script>
    <script src="/js/Roomavailability.js"></script>
</body>

</html>