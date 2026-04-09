<?php
// These Work for sure
// echo $rooms['RoomTypeName']; // Standard Single
$roomBasePrice = $rooms['BasePrice']; // 1800.00
$maxGuests = $rooms['MaxOccupancy'];  // 2
// echo $_GET['room']  // 101
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standard Rooms</title>
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
                <img id="modal-image" src="/assets/images/standard.jpg" alt="Room Image"
                    class="w-24 h-24 rounded object-cover">
                <div class="flex flex-col gap-2">
                    <h5 id="modal-room-type" class="font-crimson font-semibold text-black text-xl">Room
                        <?= htmlspecialchars($_GET['room'] ?? '') ?> - <?= $rooms['RoomTypeName'] ?> Room
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Time Duration:
                        <span id="modal-checkin-checkout">
                            <?= htmlspecialchars($checkinStr ?: 'Check-In — Check-Out') ?>
                        </span>
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Nights:
                        <span id="modal-nights" class="font-roboto text-black font-normal">1</span>
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Room Occupancy:
                        <span id="modal-guests" class="font-roboto text-black font-normal">Guest</span>
                    </h5>
                    <h5 class="font-roboto font-semibold text-black">Room Type:
                        <span id="modal-room" class="font-roboto text-black font-normal"></span>
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
                    <h6 class="font-roboto font-semibold text-black">Subtotal</h6>
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

<body data-room-type="standard">
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
                Standard Room
            </a>
        </div>

        <div class="flex justify-between gap-8">
            <div
                class="p-6 w-2/3 bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] flex flex-col gap-2 justify-between">
                <div>
                    <div class="justify-center text-black text-3xl font-normal font-crimson">Room
                        <?= htmlspecialchars($_GET['room'] ?? '') ?> - <?= $rooms['RoomTypeName'] ?> Room
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
                            src="/assets/images/standard.jpg" />
                        <img class="w-50 h-1/3 rounded-[3px] object-cover hover:scale-105 transition-transform duration-300"
                            src="/assets/images/g3.jpg" />
                        <img class="w-50 h-1/3 rounded-[3px] object-cover hover:scale-105 transition-transform duration-300"
                            src="/assets/images/g7.jpg" />
                    </div>

                    <div class="w-[70%] main-image">
                        <img class="w-full max-h-200 object-cover rounded" src="/assets/images/standard.jpg" />
                    </div>
                </div>
            </div>
            <form id="cart-form" action="/cart-submit" method="POST" data-max-guests="<?= $maxGuests ?>"
                class="w-1/3 bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] p-8 flex flex-col gap-6">
                <input type="hidden" name="roomID" value="<?= htmlspecialchars($_GET['room'] ?? '') ?>">

                <div class="flex flex-col gap-3">
                    <!-- Check-in / Check-out -->
                    <div class="justify-center text-black text-xl font-crimson font-normal">Check In - Check Out</div>
                    <input type="text" name="checkin" id="daterange" placeholder="Check-In — Check-Out"
                        value="<?= htmlspecialchars($checkinStr ?: '') ?>" required
                        class="bg-white rounded-sm p-2 text-crimson-600 font-crimson border border-gray-300">

                    <div class="text-zinc-500 text-sm">
                        Time-In 12:00 PM – Time-Out 11:00 AM
                        <p>You qualify for a 15% discount for staying more than 3 nights!</p>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <!-- Guests -->
                    <div id="guests-label" class="justify-center text-black text-xl font-crimson font-normal">
                        Guests (Max
                        <?= $maxGuests ?>)
                    </div>
                    <p class="text-sm text-gray-500" id="guests-addon">Additional guests (above 1) cost 10% of the room
                        rate
                        per night.</p>

                    <div class="flex gap-2 flex-col">
                        <!-- Adults -->
                        <div class="relative w-1/2">
                            <span class="absolute left-2 top-[13.5px] text-gray-400">
                                <img src="/assets/icons/people.svg" alt="people" />
                            </span>
                            <input type="number" id="adults" name="adults" min="1" max="<?= $maxGuests ?>" value="1"
                                placeholder="No. of Adults" required
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300">
                            <span class="ml-3 font-crimson">Adults</span>
                        </div>

                        <!-- Children -->
                        <div class="relative w-1/2">
                            <span class="absolute left-2 top-[13.5px] text-gray-400">
                                <img src="/assets/icons/people.svg" alt="people" />
                            </span>
                            <input type="number" id="children" name="children" min="0" max="<?= $maxGuests ?>" value="0"
                                placeholder="No. of Children"
                                class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300">
                            <span class="ml-3 font-crimson">Children</span>
                        </div>
                    </div>
                </div>


                <div class="flex flex-col gap-3">
                    <!-- Pricing -->
                    <div class="justify-center text-zinc-500 text-xl font-crimson font-normal">Room Rate (per night)
                    </div>
                    <div id="room-price" class="justify-center text-black text-xl font-crimson font-normal">
                        ₱ <?= number_format($roomBasePrice, 2) ?></div>

                    <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
                    <div class="justify-center text-zinc-500 text-xl font-crimson font-normal">Subtotal</div>
                    <div id="subtotal-price" class="justify-center text-black text-xl font-crimson font-normal">
                        ₱ <?= number_format($roomBasePrice, 2) ?></div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#c39c4d] p-3 hover:bg-[#3F321F] transition-colors duration-300 cursor-pointer">
                    <p
                        class="transition-all duration-300 text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                        BOOK NOW
                    </p>
                </button>
            </form>
        </div>

        <div class="pt-2">
            <div class="ml-10">
                <h1 class="justify-center text-black text-2xl font-normal font-crimson">Room Information</h1>
                <div class="h-1 w-50 bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>

            </div>
            <div class="w-full bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] p-6 flex flex-col gap-4">
                <div class="justify-center text-black text-md font-roboto font-normal">Enjoy comfort and simplicity
                    in our Standard Room, designed for a relaxing stay. Featuring cozy bedding, modern furnishings,
                    and
                    essential amenities, this room provides everything you need for a pleasant and convenient
                    experience. Perfect for solo travelers or couples looking for a comfortable space to unwind
                    after a
                    day of exploring.</div>
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
    <script>
        // Get base occupancy from PHP RoomTypeName
        const roomTypeName = "<?= $rooms['RoomTypeName'] ?>"; // e.g., "Standard Single"
        const roomBasePrice = parseFloat("<?= $roomBasePrice ?>");
        const maxGuests = parseInt($("#cart-form").data("max-guests"));
        const extraGuestRatePercent = 0.10;
        const adultsInput = $("#adults");
        const childrenInput = $("#children");

        // Force numeric input and prevent "-" or other non-digit keys
        $("#adults, #children").on("keydown", function (e) {
            // Allow only digits, backspace, delete, arrows, tab
            if (!((e.key >= "0" && e.key <= "9") ||
                ["Backspace", "ArrowLeft", "ArrowRight", "Tab", "Delete"].includes(e.key))) {
                e.preventDefault();
            }
        });

        // Clamp values dynamically and enforce maxGuests
        function enforceGuestLimits() {
            let adults = parseInt(adultsInput.val()) || 1;
            let children = parseInt(childrenInput.val()) || 0;

            // Minimum 1 adult
            if (adults < 1) adults = 1;

            // Maximum total guests
            if (adults + children > maxGuests) {
                // Prioritize adults, reduce children
                children = maxGuests - adults;
                if (children < 0) {
                    children = 0;
                    adults = maxGuests; // If adults exceed maxGuests, clamp adults
                }
            }

            adultsInput.val(adults);
            childrenInput.val(children);

            handleUpdate(); // Recalculate totals
        }

        // Trigger on typing or changing values
        adultsInput.on("input", enforceGuestLimits);
        childrenInput.on("input", enforceGuestLimits);

        // Also call once on page load
        $(document).ready(enforceGuestLimits);

        // Determine base occupancy from RoomTypeName (second word)
        let baseOccupancy = 1; // default
        const roomTypeParts = roomTypeName.split(" ");
        if (roomTypeParts.length > 1) {
            const typeWord = roomTypeParts[1].toLowerCase();
            if (typeWord === "single") baseOccupancy = 1;
            else if (typeWord === "double") baseOccupancy = 2;
            else baseOccupancy = 1; // fallback
        }

        // Function to calculate nights
        function getNights(checkInStr, checkOutStr) {
            const checkIn = new Date(checkInStr);
            const checkOut = new Date(checkOutStr);
            const diffTime = checkOut - checkIn;
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 0;
        }

        // Handle dynamic calculation
        // Handle dynamic calculation
        function handleUpdate() {
            const checkInOut = $("#daterange").val().split(" to ");
            if (checkInOut.length !== 2) return;

            const nights = getNights(checkInOut[0].replace(/\//g, "-"), checkInOut[1].replace(/\//g, "-"));
            let adults = parseInt($("#adults").val()) || 1;
            let children = parseInt($("#children").val()) || 0;

            // Validate total guests does not exceed maxGuests
            if (adults < 1) adults = 1;
            if (adults + children > maxGuests) {
                children = maxGuests - adults;
                $("#children").val(children);
            }

            // Calculate subtotal: base room rate * nights
            let subtotal = roomBasePrice * nights;

            // Extra guest charge: only guests above base occupancy
            const totalGuests = adults + children;
            let extraGuests = totalGuests - baseOccupancy;
            if (extraGuests < 0) extraGuests = 0;
            const extraGuestCharge = extraGuests * roomBasePrice * extraGuestRatePercent * nights;
            subtotal += extraGuestCharge;

            // Night discount: 15% if nights > 3
            let nightDiscount = 0;
            if (nights > 3) {
                nightDiscount = subtotal * 0.15;
                subtotal -= nightDiscount;
            }

            // Format numbers
            const format = num => num.toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Update page
            $("#subtotal-price").text("₱ " + format(subtotal));
            $("#modal-room-cost").text("₱ " + format(roomBasePrice));
            $("#modal-guest-charge").text("₱ " + format(extraGuestCharge));
            $("#modal-night-discount").text("₱ " + format(nightDiscount));
            $("#modal-total").text("₱ " + format(subtotal)); // <-- subtotal without tax
            $("#guests-addon").text(
                `Additional guests (above ${baseOccupancy}) cost 10% of the room rate per night.`
            );
        }

        // Recalculate when inputs change
        $("#daterange, #adults, #children").on("change keyup", handleUpdate);

        // Trigger on page load
        $(document).ready(function () {
            handleUpdate();
        });

        // BOOK NOW click handler
        $("#cart-form").on("submit", function (e) {
            e.preventDefault();

            const formData = $(this).serialize(); // sends roomID, adults, children, checkin

            // Recalculate totals for modal
            handleUpdate();

            // Calculate nights for modal display
            const checkInOutArr = $("#daterange").val().split(" to ");
            let nights = 0;
            if (checkInOutArr.length === 2) {
                nights = getNights(checkInOutArr[0].replace(/\//g, "-"), checkInOutArr[1].replace(/\//g, "-"));
            }

            // Update modal fields
            const roomID = $("input[name='roomID']").val();
            const roomTypeParts = "<?= $rooms['RoomTypeName'] ?>".split(" ");
            const roomType = "<?= $rooms['RoomTypeName'] ?>";

            $("#modal-room-type").text(`Room ${roomID} - ${roomType} Room`);
            $("#modal-checkin-checkout").text($("#daterange").val() || "Check-In — Check-Out");
            $("#modal-guests").text(`${parseInt($("#adults").val()) + parseInt($("#children").val())} Guest${(parseInt($("#adults").val()) + parseInt($("#children").val())) > 1 ? 's' : ''}`);
            $("#modal-room").text(roomTypeParts[1] || "Single");
            $("#modal-nights").text(nights);

            // Show modal
            $("#booking-modal").removeClass("hidden").addClass("flex");

            // Send AJAX to server for cart
            $.ajax({
                url: "/cart-submit",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        showToast("Item added to cart.")
                    } else {
                        showToast(response.error, "error");
                    }
                },
                error: function () {
                    showToast({ success: false, message: "An unexpected error occurred." });
                }
            });
        });
        // Close modal or continue browsing
        $("#close-modal, #continue-browsing").on("click", function () {
            // Hide modal
            $("#booking-modal").removeClass("flex").addClass("hidden");
            // Optional: scroll back to the main page content if needed
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Proceed to checkout
        $("#proceed-checkout").on("click", function () {
            window.location.href = "/cart";
        });
    </script>
</body>

</html>