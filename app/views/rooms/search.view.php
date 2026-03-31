<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Rooms</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>

<!-- TODO: Do not show in search if already booked -->
<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="flex-1 py-10 px-30 flex flex-col gap-5">
        <div class="justify-start text-black text-lg font-normal font-crimson">
            <a href="/home" class="hover:underline">Home</a> &gt; Search Rooms
        </div>

        <h1 class="font-crimson font-bold text-3xl">SEARCH ROOMS</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <div
                class="flex-1 bg-linear-to-br from-stone-700 to-yellow-100 rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] max-h-98">
                <form method="POST" action="/search">

                    <div class="flex flex-col gap-2 p-5">
                        <div class="relative">
                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/calendar.svg" alt="calendar">
                            </span>

                            <input type="text" name="checkin" id="daterange" placeholder="Check-In — Check-Out" required
                                class="bg-white rounded-sm p-2 pl-9 text-crimson-600 font-crimson border border-gray-300 w-full"
                                value="<?= isset($_POST['checkin']) ? htmlspecialchars($_POST['checkin']) : '' ?>">
                        </div>

                        <div class="relative">

                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/people.svg" alt="people">
                            </span>
                            <input type="number" id="adults" name="adults" placeholder="No. of Adults" min="0"
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 w-full"
                                value="<?= !empty($_POST['adults']) ? (int) $_POST['adults'] : '' ?>">
                        </div>

                        <div class="relative">
                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/people.svg" alt="people">
                            </span>
                            <input type="number" id="children" name="children" placeholder="No. of Children" min="0"
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 w-full"
                                value="<?= !empty($_POST['children']) ? (int) $_POST['children'] : '' ?>">
                        </div>

                        <!-- Room Type Radios -->
                        <div class="flex items-center gap-4 font-crimson">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="radio" name="room" value="single" data-base-price="1800"
                                    class="peer sr-only" <?= (isset($_POST['room']) && $_POST['room'] === 'single') ? 'checked' : '' ?>>
                                <div class="w-5 h-5 border-2 border-white rounded-full shrink-0
                    peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                                <span class="ml-2 text-white">Single</span>
                            </label>

                            <label class="relative flex items-center cursor-pointer">
                                <input type="radio" name="room" data-base-price="2700" value="double"
                                    class="peer sr-only" <?= (isset($_POST['room']) && $_POST['room'] === 'double') ? 'checked' : '' ?>>
                                <div class="w-5 h-5 border-2 border-white rounded-full shrink-0
                    peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                                <span class="ml-2 text-white">Double</span>
                            </label>
                        </div>

                        <!-- Room Type Select -->
                        <select name="room_type" class="border p-2 rounded font-crimson">
                            <option value="">Any Type</option>
                            <?php foreach ($roomTypes as $type): ?>
                                <option value="<?= $type['RoomTypeID'] ?>" <?= (isset($_POST['room_type']) && $_POST['room_type'] == $type['RoomTypeID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['RoomTypeName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div class="relative">

                            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
                                <img src="/assets/icons/bed.svg" alt="people">
                            </span>
                            <input type="number" id="beds" name="beds" placeholder="No. of Beds" min="0"
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 w-full"
                                value="<?= !empty($_POST['beds']) ? (int) $_POST['beds'] : '' ?>">
                        </div>

                        <!-- Clear Filters Button -->
                        <div class="flex justify-end relative z-50">
                            <button type="button" id="clearFilters" class="text-white underline font-roboto text-sm">
                                Clear Filters
                            </button>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                            class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#714623] p-3">
                            SEARCH ROOMS
                        </button>
                    </div>
                </form>

            </div>
            <div class="flex-3 rounded-[1px] border-[0.30px] border-zinc-500">
                <div class="p-5">
                    <!-- TODO: Sort by -->
                    <div class="justify-start text-black text-xs font-light font-['Roboto']">Sort by:</div>
                </div>

                <!-- SEARCH RESULTS -->
                <div class="flex flex-col gap-4 p-5">
                    <?php
                    $roomDescriptions = [
                        'Standard Single' => "Enjoy comfort and simplicity in our Standard Room, designed for a relaxing stay. Featuring cozy bedding, modern furnishings, and essential amenities for a pleasant experience...",
                        'Standard Double' => "Enjoy comfort and simplicity in our Standard Room, designed for a relaxing stay. Featuring cozy bedding, modern furnishings, and essential amenities for a pleasant experience...",

                        'Deluxe Single' => "Indulge in luxury with our Deluxe Room, offering spacious interiors, premium bedding, elegant décor, and enhanced amenities for a truly elevated stay...",
                        'Deluxe Double' => "Indulge in luxury with our Deluxe Room, offering spacious interiors, premium bedding, elegant décor, and enhanced amenities for a truly elevated stay...",

                        'Suite Single' => "Experience ultimate comfort in our Suite, featuring separate living space, upscale furnishings, and exclusive amenities perfect for extended stays or special occasions...",
                        'Suite Double' => "Experience ultimate comfort in our Suite, featuring separate living space, upscale furnishings, and exclusive amenities perfect for extended stays or special occasions..."
                    ];
                    $roomPics = [
                        "Standard Single" => "/assets/images/standard.jpg",
                        "Standard Double" => "/assets/images/standard.jpg",
                        "Deluxe Single" => "/assets/images/deluxe.jpg",
                        "Deluxe Double" => "/assets/images/deluxe.jpg",
                        "Suite Single" => "/assets/images/suite.jpg",
                        "Suite Double" => "/assets/images/suite.jpg"
                    ];
                    ?>
                    <?php foreach ($rooms as $room): ?>
                        <div class="mx-auto h-0.5 w-full bg-yellow-900/60 rounded-lg"></div>

                        <div class="flex gap-3 my-2">
                            <div class="flex-1">
                                <img src="<?= htmlspecialchars($roomPics[$room['RoomTypeName']] ?? '/assets/images/standard.jpg') ?>"
                                    alt="Room Image">
                            </div>

                            <div class="flex-3 flex flex-col gap-3">
                                <h2 class="font-crimson font-medium text-3xl">
                                    Room <?= htmlspecialchars($room['RoomNumber']) ?> -
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
                                        <p>Max Occupancy: <?= htmlspecialchars($room['MaxOccupancy']) ?></p>
                                    </div>
                                    <p class="font-crimson text-2xl">₱<?= htmlspecialchars($room['BasePrice']) ?>/Per Night
                                    </p>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3">
                                        <a
                                            href="/room/standard?type=<?= urlencode($room['RoomTypeName']) ?>&room=<?= $room['RoomNumber'] ?>&checkin=<?= urlencode($_POST['checkin'] ?? '') ?>">
                                            VIEW MORE
                                        </a>
                                    </button>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($rooms)): ?>
                        <p class="text-red-500">No rooms match your criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/daterange.js"></script>
    <script src="/js/clearFilter.js"></script>
</body>

</html>