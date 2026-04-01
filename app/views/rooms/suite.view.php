<?php
$rooms = [
    'standard' => [
        'single' => ['price' => 1800, 'maxGuests' => 2],
        'double' => ['price' => 2700, 'maxGuests' => 3],
    ],
    'deluxe' => [
        'single' => ['price' => 2300, 'maxGuests' => 4],
        'double' => ['price' => 3200, 'maxGuests' => 6],
    ],
    'suite' => [
        'single' => ['price' => 3000, 'maxGuests' => 6],
        'double' => ['price' => 4000, 'maxGuests' => 10],
    ],
];

// Set the page room category manually
// For standard.php page:
// $roomCategory = 'standard';

// For deluxe.php page:
$roomCategory = 'deluxe';

// For suite.php page:
$roomCategory = 'suite';

// Occupancy type comes from GET or defaults to single
$occupancy = strtolower($_GET['type'] ?? 'single');
if (!in_array($occupancy, ['single', 'double'])) {
    $occupancy = 'single';
}

// Fetch room config safely
$roomConfig = $rooms[$roomCategory][$occupancy];
$roomBasePrice = $roomConfig['price'];
$maxGuests = $roomConfig['maxGuests'];

// Checked attributes for the radio buttons
$singleChecked = $occupancy === 'single' ? 'checked' : '';
$doubleChecked = $occupancy === 'double' ? 'checked' : '';

// Check-in / Check-out parsing
$checkinStr = $_GET['checkin'] ?? '';
$checkin = '';
$checkout = '';

if (!empty($checkinStr) && strpos($checkinStr, ' to ') !== false) {
    [$checkin, $checkout] = explode(' to ', $checkinStr, 2);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suite Rooms</title>
    <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<!-- Booking Confirmation Modal -->
<div id="booking-modal" class="fixed inset-0 hidden items-start pt-15 justify-center bg-[rgba(0,0,0,0.7)]  z-50">
    <div class="flex">
        <!-- Left Side -->
        <div class="bg-white rounded-l-sm shadow-lg w-150 p-6 flex flex-col gap-4 relative">
            <div class="flex items-center space-x-2">
                <img src="/assets/icons/check.svg" alt="check" class="w-5 h-5">
                <div class="text-[#6D9858] text-xl font-normal font-crimson">Room successfully added to your cart</div>
            </div>
            <div class="flex gap-4">
                <img id="modal-image" src="/assets/images/suite.jpg" alt="Room Image"
                    class="w-24 h-24 rounded object-cover">
                <div class="flex flex-col gap-2">
                    <h5 id="modal-room-type" class="font-crimson font-semibold text-black">Room
                        <?= htmlspecialchars($_GET['room'] ?? '') ?> - Suite Room
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Time Duration:
                        <span id="modal-checkin-checkout">
                            <?= htmlspecialchars($checkinStr ?: 'Check-In — Check-Out') ?>
                        </span>
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Room Occupancy:
                        <span id="modal-guests" class="font-roboto text-black font-normal">1 Guest</span>
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Room Type:
                        <span id="modal-room" class="font-roboto text-black font-normal">Single</span>
                    </h5>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="bg-[#F7F7F7] rounded-r-sm shadow-lg w-150 p-6 flex flex-col gap-4 relative">
            <button id="close-modal"
                class="absolute top-1 right-2 text-gray-500 hover:text-gray-800 font-bold cursor-pointer">×</button>
            <div class="text-black text-xl font-normal font-crimson">1 item in your cart</div>
            <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
            <div class="flex flex-col gap-2">
                <div class="flex justify-between">
                    <h6 class="font-roboto font-semibold text-black">Room cost per Night:</h6>
                    <p id="modal-room-cost" class="font-roboto font-normal text-black">₱1,800.00</p>
                </div>
                <div class="flex justify-between">
                    <h6 class="font-roboto font-semibold text-black">Night Discount:</h6>
                    <p id="modal-night-discount" class="font-roboto font-normal text-black">₱0.00</p>
                </div>
                <div class="flex justify-between">
                    <h6 class="font-roboto font-semibold text-black">Additional Guest Charge:</h6>
                    <p id="modal-guest-charge" class="font-roboto font-normal text-black">₱0.00</p>
                </div>
                <div class="flex justify-between">
                    <h6 class="font-roboto font-semibold text-black">Total (12% tax incl.)</h6>
                    <p id="modal-total" class="font-roboto font-normal text-black">₱2,016.00</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button id="continue-browsing"
                    class="bg-gray-300 hover:bg-gray-400 text-black font-semibold px-4 py-2 rounded cursor-pointer">Continue
                    Browsing</button>
                <button id="proceed-checkout" class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#c39c4d] p-3 
           hover:bg-[#3F321F] transition-colors duration-300 cursor-point">
                    <p
                        class="transition-all duration-300 text-white hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                        Proceed to Checkout
                    </p>
                </button>
            </div>
        </div>
    </div>
</div>

<body data-room-type="suite">
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="py-10 px-30 flex flex-col gap-5">
        <div class="flex text-black text-base font-normal font-crimson">
            <!-- Home button with SVG -->
            <a href="/home" class="flex items-center border border-neutral-300 px-4 py-1">
                <img src="/assets/icons/home.svg" alt="Home" class="w-4 h-4">
            </a>

            <a href="/search" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1">
                Search
            </a>

            <a href="/home#rooms" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1">
                Rooms
            </a>

            <a href="" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Suite Room
            </a>
        </div>

        <div class="flex justify-between gap-8">
            <div
                class="p-6 w-2/3 bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex flex-col gap-2 justify-between">
                <div>
                    <div class="justify-center text-black text-3xl font-normal font-crimson">Room
                        <?= htmlspecialchars($_GET['room'] ?? '') ?> - Suite Room
                    </div>
                    <div class="flex items-center justify-start">
                        <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                        <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                        <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                        <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                        <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                        <h6 class="font-normal font-roboto ml-3">1 Review</h6>
                    </div>
                </div>
                <div class="flex gap-6">
                    <div class="w-[20%] flex flex-col gap-4 left-thumbnails">
                        <img class="w-50 h-1/3 rounded-[3px] object-cover  hover:scale-105 transition-transform duration-300"
                            src="/assets/images/suite.jpg" />
                        <img class="w-50 h-1/3 rounded-[3px] object-cover hover:scale-105 transition-transform duration-300"
                            src="/assets/images/g3.jpg" />
                        <img class="w-50 h-1/3 rounded-[3px] object-cover hover:scale-105 transition-transform duration-300"
                            src="/assets/images/g7.jpg" />
                    </div>

                    <div class="w-[80%] main-image">
                        <img class="w-full max-h-200 object-cover rounded" src="/assets/images/suite.jpg" />
                    </div>
                </div>
            </div>
            <form id="cart-form" action="/cart-submit" method="POST"
                class="w-1/3 bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] p-6 flex flex-col gap-6">
                <input type="hidden" name="roomID" id="roomID" value="<?= htmlspecialchars($_GET['room'] ?? '') ?>">
                <div class="justify-center text-black text-xl font-normal font-crimson">Check In - Check Out</div>
                <input type="text" name="checkin" id="daterange" placeholder="Check-In — Check-Out"
                    value="<?= htmlspecialchars($checkinStr ?: '') ?>" required
                    class="bg-white rounded-sm p-2 text-crimson-600 font-crimson border border-gray-300">
                <div>
                    <div class="justify-center text-zinc-500 text-lg font-normal font-crimson">Time-In 12:00 PM –
                        Time-Out
                        11:00 AM</div>
                    <p class="text-zinc-500 font-normal text-xs">You qualify for a 15% discount for staying more
                        than 3
                        nights!
                    </p>

                </div>
                <div>
                    <!-- TODO: Add Children and Adults -->
                    <div id="guests-label" class="justify-center text-black text-xl font-normal font-crimson">
                        Guests (Max
                        <?= $maxGuests ?>)
                    </div>
                    <p class="text-sm text-gray-500" id="guests-addon">Additional guests (above 1) cost 10% of the
                        room
                        rate
                        per
                        night.</p>
                </div>
                <input type="number" id="guests" name="adults" min="1" max="<?= $maxGuests ?>" value="1"
                    placeholder="Guests" required
                    class="bg-white rounded-sm p-2 text-crimson-600 font-crimson border border-gray-300">
                <div class="justify-center text-black text-xl font-normal font-crimson">Room Type</div>
                <div class="flex items-center gap-4">
                    <label class="relative flex items-center cursor-pointer">
                        <input type="radio" name="room" value="single"
                            data-base-price="<?= $rooms[$roomCategory]['single']['price'] ?>"
                            data-max-guests="<?= $rooms[$roomCategory]['single']['maxGuests'] ?>" <?= $singleChecked ?>
                            required class="peer sr-only">
                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full shrink-0
        peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                        <span class="ml-2 text-gray-700 font-roboto">Single</span>
                    </label>

                    <label class="relative flex items-center cursor-pointer">
                        <input type="radio" name="room" value="double"
                            data-base-price="<?= $rooms[$roomCategory]['double']['price'] ?>"
                            data-max-guests="<?= $rooms[$roomCategory]['single']['maxGuests'] ?>" <?= $doubleChecked ?>
                            class="peer sr-only">
                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full shrink-0
        peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                        <span class="ml-2 text-gray-700 font-roboto">Double</span>
                    </label>
                </div>
                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
                <div class="justify-center text-zinc-500 text-xl font-normal font-crimson">Room Rate (per night)
                </div>
                <div id="room-price" class="justify-center text-black text-xl font-normal font-crimson">
                    ₱<?= number_format($roomBasePrice) ?></div>
                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
                <div class="justify-center text-zinc-500 text-xl font-normal font-crimson">Subtotal</div>
                <div id="subtotal-price" class="justify-center text-black text-xl font-normal font-crimson">
                    ₱<?= number_format($roomBasePrice) ?>
                </div>
                <button type="submit" class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#c39c4d] p-3 
           hover:bg-[#3F321F] transition-colors duration-300 cursor-pointer">
                    <p
                        class="transition-all duration-300 text-white hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                        BOOK NOW
                    </p>
                </button>
            </form>
        </div>

        <div class="pt-2">
            <div class="ml-10">
                <h1 class="justify-center text-black text-2xl font-normal font-crimson">Room Information</h1>
                <!-- TODO: ADDITIONAL: reviews -->
                <!-- <h1>Reviews</h1> -->
                <div class="h-1 w-50 bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>

            </div>
            <div class="w-full bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] p-6 flex flex-col gap-4">
                <div class="justify-center text-black text-md font-roboto font-normal">Enjoy comfort and simplicity
                    in our Suite Room, designed for a relaxing stay. Featuring cozy bedding, modern furnishings,
                    and
                    essential amenities, this room provides everything you need for a pleasant and convenient
                    experience. Perfect for solo travelers or couples looking for a comfortable space to unwind
                    after a
                    day of exploring.</div>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">MAX NO. GUESTS</div>
                <ul class="text-black text-md font-normal font-roboto">
                    <li>Single – 2 Guests</li>
                    <li>Double – 3 Guests</li>
                </ul>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">CHECK-IN AND CHECK-OUT
                    TIME
                    <ul class="text-black text-md font-normal font-roboto">
                        <li>Check-in: 12:00 PM</li>
                        <li>Check-out: 11:00 AM</li>
                    </ul>
                </div>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">BED TYPES</div>
                <ul class="text-black text-md font-normal font-roboto">
                    <li>2 * Single Bed</li>
                </ul>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">ROOM FEATURES</div>
                <ul class="text-black text-md font-normal font-roboto">
                    <li class="flex items-center space-x-2">
                        <img src="/assets/icons/wifi.svg" alt="Wi-Fi Icon" class="w-5 h-5">
                        <span>Wi-Fi</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <img src="/assets/icons/AC.svg" alt="Air Condition Icon" class="w-5 h-5">
                        <span>Air Condition</span>
                    </li>
                </ul>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">HOTEL DESCRIPTION</div>
                <div class="justify-center text-black text-2xl font-roboto font-bold">Welcome to Subic Riviera!
                </div>
                <div class="justify-center text-black text-lg font-roboto font-normal">Experience elegance, comfort,
                    and
                    seaside relaxation at Subic Riviera. Located near the beautiful coastline of Subic, our hotel
                    offers
                    a perfect escape for travelers looking to unwind while enjoying modern luxury. Whether you are
                    visiting for leisure, business, or a special event, Subic Riviera provides a memorable stay with
                    exceptional service, stylish accommodations, and premium facilities. </div>
                <div class="justify-center text-black text-lg font-roboto font-semibold">Benefits of Staying at
                    Riviera!
                </div>
                <div class="justify-center text-black text-md font-roboto italic">Accommodation:</div>
                <div class="justify-center text-black text-md font-roboto">Relax in our well-designed rooms and
                    suites
                    that combine comfort with modern elegance. Each room is thoughtfully furnished with plush
                    bedding,
                    individually controlled air conditioning, high-speed Wi-Fi, a smart TV, and spacious interiors
                    that
                    create a calm and relaxing atmosphere. After a day of exploring Subic or attending meetings, our
                    rooms provide the perfect place to rest and recharge.</div>
                <div class="justify-center text-black text-md font-roboto italic">Dining:</div>
                <div class="justify-center text-black text-md font-roboto">Enjoy a unique dining experience at our
                    rooftop bar and dining area, where guests can savor a variety of refreshing drinks and delicious
                    dishes while overlooking scenic views. Our menu offers a selection of local and international
                    flavors, perfect for casual meals, sunset drinks, or a relaxing evening with friends and family.
                </div>
                <div class="justify-center text-black text-md font-roboto italic">Facilities:</div>
                <div class="justify-center text-black text-md font-roboto">Subic Riviera offers a variety of
                    facilities
                    designed to enhance your stay. Take a refreshing swim in our outdoor swimming pool, enjoy
                    stunning
                    views while relaxing at the rooftop pool and bar, or stay active in our fully equipped fitness
                    gym.
                    Guests can also enjoy comfortable lounge areas and beautifully designed spaces perfect for
                    relaxation.</div>
                <div class="justify-center text-black text-md font-roboto italic">Events and Meetings:</div>
                <div class="justify-center text-black text-md font-roboto">Host memorable events at our spacious and
                    versatile event halls. Whether you are planning a corporate meeting, conference, wedding
                    reception,
                    or private celebration, our event venues provide the ideal setting. Equipped with modern
                    audiovisual
                    technology and supported by our dedicated event staff, we ensure every event is seamless and
                    successful.</div>
                <div class="justify-center text-black text-md font-roboto italic">Location:</div>
                <div class="justify-center text-black text-md font-roboto">Situated close to the sea, Subic Riviera
                    allows guests to enjoy the refreshing ocean breeze and easy access to nearby coastal
                    attractions.
                    Guests can explore the nearby shoreline, enjoy beautiful sunsets, and visit shopping, dining,
                    and
                    entertainment spots within the Subic area.</div>
                <div class="justify-center text-black text-md font-roboto italic">Exceptional Service:</div>
                <div class="justify-center text-black text-md font-roboto">At Subic Riviera, we pride ourselves on
                    delivering exceptional service. From check-in to check-out, our friendly and professional staff
                    are
                    dedicated to ensuring your stay is comfortable, convenient, and truly memorable.</div>
                <br>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">HOTEL POLICIES</div>
                <ul class="list-disc text-black text-md font-normal font-roboto ml-5">
                    <li>Accommodation will only be provided to guests whose details are registered at the hotel
                        front
                        desk.</li>
                    <li>Guests must present a valid photo identification during check-in.</li>
                    <li>Applicable taxes and government charges may apply.</li>
                    <li>Full or advance payment may be required upon check-in.</li>
                    <li>Standard check-in time is 12:00 PM and check-out time is 11:00 AM. Early check-in and late
                        check-out are subject to availability.</li>
                    <li>The hotel reserves the right to refuse accommodation to guests who do not comply with hotel
                        policies and regulations.</li>
                    <li>Guests are responsible for any damage or loss caused to hotel property during their stay.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/thumbnail.js"></script>
    <script src="/js/daterange.js"></script>
    <script src="/js/calculateTotalAmount.js"></script>
    <script src="/js/bookingModal.js"></script>
    <script src="/js/maxGuests.js"></script>
</body>

</html>