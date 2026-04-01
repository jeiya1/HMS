<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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

            <!-- Cart button with text -->
            <a href="/cart" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Cart
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">YOUR BOOKING CART</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <?php if (!empty($carts)): ?>
            <div id="cart-summary-section" class="flex justify-center gap-5 font-roboto">
                <div class="flex flex-col w-3/5 gap-4">
                    <div id="rooms-section" class="section flex flex-col border-[0.3px] border-zinc-300 rounded p-8">
                        <div class="section-header flex justify-between">
                            <h1 class="font-semibold font-crimson text-3xl mb-1">Rooms & Price Summary</h1>
                            <button class="toggle-btn">
                                <img src="/assets/icons/left-arrow-black.svg" alt="Back" class="w-5 h-5 cursor-pointer">
                            </button>
                        </div>
                        <p class="text-neutral-700 font-light font-crimson">Rooms information.</p>

                        <!-- CONTENT -->
                        <div class="section-content mt-4">
                            <div class="flex flex-col gap-2">
                                <div id="cart-items-container" class="flex flex-col gap-5 font-roboto mb-3">
                                    <?php foreach ($carts as $index => $cart): ?>
                                        <?php
                                        $roomType = $cart['RoomTypeName'];
                                        $imagePath = '/assets/images/default.jpg';

                                        if ($roomType === 'Standard Single' || $roomType === 'Standard Double') {
                                            $imagePath = '/assets/images/standard.jpg';
                                        }
                                        if ($roomType === 'Deluxe Single' || $roomType === 'Deluxe Double') {
                                            $imagePath = '/assets/images/deluxe.jpg';
                                        }
                                        if ($roomType === 'Suite Single' || $roomType === 'Suite Double') {
                                            $imagePath = '/assets/images/suite.jpg';
                                        }
                                        ?>
                                        <div class="flex gap-8">
                                            <div class="flex-1"> <img src="<?= $imagePath ?>" alt="Room Image"
                                                    class="w-50 object-cover rounded"> <button
                                                    class="font-roboto text-neutral-700 px-3 py-1 rounded mt-2 delete-cart-item flex items-center gap-2"
                                                    data-cartroomid="<?php echo $cart['CartRoomID']; ?>"> <span
                                                        class="inline-flex items-center"> <img src="/assets/icons/delete.svg"
                                                            alt="Delete" class="w-4 h-4 cursor-pointer"> </span>
                                                    <p class="cursor-pointer">Remove </p>
                                                </button> </div>
                                            <div class="flex-5 flex flex-col gap-3">
                                                <h2 class="font-bold font-crimson text-xl">
                                                    <?php echo htmlspecialchars($cart['RoomTypeName']); ?> Room #
                                                    <?php echo htmlspecialchars($cart['RoomNumber']); ?>
                                                </h2>
                                                <div class="flex gap-2">
                                                    <div
                                                        class="w-22 h-6 bg-linear-to-l from-yellow-100 to-yellow-700 rounded-[100px] flex justify-center items-center">
                                                        <img src="/assets/icons/wifi.svg" alt="Wi-Fi Icon" class="w-5 h-5">
                                                        <span
                                                            class="justify-center text-black text-xs font-light font-roboto mx-2">Wi-Fi</span>
                                                    </div>
                                                    <div
                                                        class="w-34 h-6 bg-linear-to-l from-yellow-100 to-yellow-700 rounded-[100px] flex justify-center items-center">
                                                        <img src="/assets/icons/AC.svg" alt="Wi-Fi Icon" class="w-5 h-5">
                                                        <span
                                                            class="justify-center text-black text-xs font-light font-roboto mx-2">Air
                                                            Conditioning
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-full h-16 bg-[#FDF1C4] rounded-[3px] py-3 px-5 flex gap-30">
                                                    <div class="flex flex-col gap-1">
                                                        <div
                                                            class="justify-center text-stone-800 text-xs font-semibold font-crimson">
                                                            CHECK IN</div>
                                                        <div
                                                            class="justify-center text-black text-base font-normal font-roboto">
                                                            <?php echo htmlspecialchars($cart['CheckInDate']); ?> (12:00 PM)
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-col gap-1">
                                                        <div
                                                            class="justify-center text-stone-800 text-xs font-semibold font-crimson">
                                                            CHECK OUT</div>
                                                        <div
                                                            class="justify-center text-black text-base font-normal font-roboto">
                                                            <?php echo htmlspecialchars($cart['CheckOutDate']); ?> (11:00 AM)
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-col gap-1">
                                                        <div
                                                            class="justify-center text-stone-800 text-xs font-semibold font-crimson">
                                                            OCCUPANCY</div>
                                                        <div
                                                            class="justify-center text-black text-base font-normal font-roboto">
                                                            <?php echo htmlspecialchars($cart['NumAdults']); ?> Adults
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end">
                                                    <div class="w-48 h-22 bg-black/5 rounded-[3px] p-2">
                                                        <p
                                                            class="flex justify-end text-black text-2xl font-normal font-roboto mt-2">
                                                            ₱
                                                            <?php echo htmlspecialchars($cart['BasePrice']); ?>
                                                        </p>
                                                        <p
                                                            class="flex justify-end text-zinc-500 text-sm font-normal font-crimson">
                                                            Room rate for 1 Night(s) stay</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                            <div class="flex justify-end mt-3">
                                <button id="to-guest"
                                    class="flex items-center justify-center gap-2 text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 w-40 align-middle group cursor-pointer hover:bg-[#3F321F] transition-colors">
                                    <p
                                        class="transition-all duration-300 text-white group-hover:text-white group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                                        PROCEED
                                    </p>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center gap-5 font-roboto">
                        <div id="guest-section"
                            class="section flex flex-col border-[0.3px] border-zinc-300 rounded p-8 w-full">
                            <div class="section-header flex justify-between">
                                <h1 class="font-bold font-crimson text-3xl mb-1">Guest Information</h1>
                                <button class="toggle-btn">
                                    <img src="/assets/icons/left-arrow-black.svg" alt="Back" class="w-5 h-5 cursor-pointer">
                                </button>
                            </div>
                            <div class="section-content hidden mt-2">
                                <?php if ($logged_in): ?>
                                    <script>
                                        // Pass logged-in user details to JS
                                        const loggedInUserData = {
                                            firstName: <?= json_encode($_SESSION['logged_in_user_firstName'] ?? $user->FirstName ?? '') ?>,
                                            lastName: <?= json_encode($_SESSION['logged_in_user_lastName'] ?? $user->LastName ?? '') ?>,
                                            email: <?= json_encode($_SESSION['logged_in_user_email'] ?? $user->Email ?? '') ?>,
                                            phone: <?= json_encode($_SESSION['logged_in_user_phone'] ?? $user->PhoneContact ?? '') ?>,
                                            birthDate: <?= json_encode($_SESSION['logged_in_user_birthDate'] ?? $user->BirthDate ?? '') ?>
                                        };
                                    </script>
                                    <div class="form-group mb-4">
                                        <input type="checkbox" id="use-account-details" class="cursor-pointer"
                                            data-user-id="<?= $_SESSION['logged_in_user_id'] ?? '' ?>">
                                        <span>Use my account details</span>
                                    </div>

                                    <form id="reservation-form" action="/signup-submit" method="POST" class="mb-5">
                                        <div class="flex justify-between gap-5">
                                            <div class="flex flex-col gap-2 w-full">
                                                <label for="fname">First Name* </label>
                                                <input type="text" id="fname" name="fname" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                                <label for="phone">Phone Contact* </label>
                                                <div class="flex gap-2">
                                                    <select name="country_code" id="country_code" required
                                                        class="border border-gray-300 p-2 rounded bg-white text-black w-28">
                                                        <option value="+63" selected>🇵🇭 +63</option>
                                                        <option value="+1">🇺🇸 +1</option>
                                                        <option value="+44">🇬🇧 +44</option>
                                                        <option value="+61">🇦🇺 +61</option>
                                                    </select>

                                                    <input type="tel" name="phone" id="phone" placeholder="9123456789"
                                                        pattern="[0-9]{7,12}" required
                                                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                                </div>
                                                <label for="birthDate">Birthdate* </label>
                                                <input type="text" id="birthDate" name="birthDate" id="birthDate"
                                                    placeholder="Select your birthdate" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                                <label for="email">Email: </label>
                                                <input type="email" name="email" id="email" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                            </div>
                                            <div class="flex flex-col gap-2 w-full">
                                                <label for="lname">Last Name* </label>
                                                <input type="text" id="lname" name="lname" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                            </div>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <p class="text-neutral-700 font-light font-crimson mb-2">Already have an account? <span
                                            class="text-yellow-900 text-xs font-normal font-['Roboto']"> <a
                                                href="/registration">Log in</a> </span>now to
                                        make
                                        checkout
                                        process faster and time saving.</p>

                                    <form id="reservation-form" action="/signup-submit" method="POST" class="mb-5">
                                        <div class="flex justify-between gap-5">
                                            <div class="flex flex-col gap-2 w-full">
                                                <label for="fname">First Name* </label>
                                                <input type="text" id="fname" name="fname" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                                <label for="phone">Phone Contact* </label>
                                                <div class="flex gap-2">
                                                    <select name="country_code" id="country_code" required
                                                        class="border border-gray-300 p-2 rounded bg-white text-black w-28">
                                                        <option value="+63" selected>🇵🇭 +63</option>
                                                        <option value="+1">🇺🇸 +1</option>
                                                        <option value="+44">🇬🇧 +44</option>
                                                        <option value="+61">🇦🇺 +61</option>
                                                    </select>

                                                    <input type="tel" name="phone" id="phone" placeholder="9123456789"
                                                        pattern="[0-9]{7,12}" required
                                                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                                </div>
                                                <label for="birthDate">Birthdate* </label>
                                                <input type="text" id="birthDate" name="birthDate" id="birthDate"
                                                    placeholder="Select your birthdate" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                                <label for="email">Email: </label>
                                                <input type="email" name="email" id="email" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                            </div>
                                            <div class="flex flex-col gap-2 w-full">
                                                <label for="lname">Last Name* </label>
                                                <input type="text" id="lname" name="lname" required
                                                    class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                                <div class="flex justify-end mt-3">
                                    <button id="to-payment"
                                        class="flex items-center justify-center gap-2 text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 w-40 align-middle group cursor-pointer hover:bg-[#3F321F] transition-colors">
                                        <p
                                            class="transition-all duration-300 text-white group-hover:text-white group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                                            PROCEED
                                        </p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center gap-5 font-roboto">
                        <div id="payment-section"
                            class="section flex flex-col border-[0.3px] border-zinc-300 rounded p-8 w-full">
                            <div class="section-header flex justify-between">
                                <h1 class="font-bold font-crimson text-3xl mb-1">Payment Information</h1>
                                <button class="toggle-btn">
                                    <img src="/assets/icons/left-arrow-black.svg" alt="Back" class="w-5 h-5 cursor-pointer">
                                </button>
                            </div>
                            <div class="section-content hidden my-2">
                                <p class="text-neutral-700 font-light font-crimson mb-4">How would you like to pay?</p>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <!-- Debit/Credit Card -->
                                    <label
                                        class="flex items-center border-[0.5px] border-zinc-300 rounded p-4 cursor-pointer">
                                        <img src="/assets/icons/card.svg" alt="Card Icon" class="w-5 h-5 mr-2">
                                        <span class="text-black text-xs font-normal font-roboto">Debit/Credit Card</span>
                                        <input type="radio" name="payment" value="Card" class="accent-[#714623] ml-auto">
                                    </label>

                                    <!-- Cash -->
                                    <label
                                        class="flex items-center border-[0.5px] border-zinc-300 rounded p-4 cursor-pointer">
                                        <img src="/assets/icons/cash.svg" alt="Cash Icon" class="w-5 h-5 mr-2">
                                        <span class="text-black text-xs font-normal font-roboto">Cash</span>
                                        <input type="radio" name="payment" value="Cash" class="accent-[#714623] ml-auto">
                                    </label>

                                    <!-- E-Wallet -->
                                    <label
                                        class="flex items-center border-[0.5px] border-zinc-300 rounded p-4 cursor-pointer">
                                        <img src="/assets/icons/qr.svg" alt="E-wallet Icon" class="w-5 h-5 mr-2">
                                        <span class="text-black text-xs font-normal font-roboto">QR-Ph</span>
                                        <input type="radio" name="payment" value="E-Wallet"
                                            class="accent-[#714623] ml-auto">
                                    </label>
                                </div>

                                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                                <div class="flex justify-end mt-3">
                                    <button id="to-checkout"
                                        class="flex items-center justify-center gap-2 text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 w-40 align-middle group cursor-pointer hover:bg-[#3F321F] transition-colors">
                                        <p
                                            class="transition-all duration-300 text-white group-hover:text-white group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] cursor-pointer">
                                            PROCEED
                                        </p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="self-start flex flex-col border-[0.3px] border-zinc-300 rounded w-2/5 p-8" id="cart-summary-right">
                    <h2 class="font-semibold font-crimson text-2xl mb-4">Price Breakdown</h2>
                    <div class="text-sm font-roboto" id="price-breakdown-list"></div>
                    <div class="mt-4 font-semibold text-lg" id="cart-total">Total: ₱0.00</div>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col border rounded p-4 shadow w-full max-w-xl gap-2">
                <h1 class="font-bold">No booking found in cart</h1>
                <p class="italic">You have not added any rooms or products to your cart yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 min-h-screen overflow-auto opacity-0 pointer-events-none transition-opacity duration-200">
        <!-- <div id="payment-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"> -->
        <div class="bg-white rounded-lg w-96 p-6 relative">
            <button id="modal-close"
                class="absolute top-3 right-3 text-gray-500 hover:text-black cursor-pointer">&times;</button>
            <h2 class="text-xl font-bold mb-4" id="payment-modal-title">Payment</h2>
            <div id="payment-modal-content" class="space-y-4">
                <!-- Dynamic content will be injected here -->
            </div>
            <div class="flex justify-end mt-4">
                <button id="modal-pay"
                    class="text-white font-roboto text-[14px] font-semibold leading-normal rounded-sm bg-[#714623] p-2.5 cursor-pointer hover:bg-[#654022] transition-colors duration-300 group">
                    <p class="group-hover:text-white group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)]">
                        CONFIRM & PAY
                    </p>
                </button>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script>
        $(document).ready(function () {

            // ==================== SECTION ACCORDION ====================
            function activateSection(sectionId) {
                $('.section-content').slideUp(200);
                $('.toggle-btn img').removeClass('rotate-270');

                const section = $(sectionId);
                section.find('.section-content').slideDown(200);
                section.find('.toggle-btn img').addClass('rotate-270');
            }

            activateSection('#rooms-section');

            $('#to-guest').click(() => activateSection('#guest-section'));
            $('#to-payment').click(() => activateSection('#payment-section'));

            $('.section-header').click(function () {
                const section = $(this).closest('.section');
                const content = section.find('.section-content');
                const arrow = section.find('.toggle-btn img');

                content.slideToggle(200);
                arrow.toggleClass('rotate-270');
            });

            // ==================== CART TOTAL CALCULATION ====================
            function getNights(checkIn, checkOut) {
                const d1 = new Date(checkIn);
                const d2 = new Date(checkOut);
                const diffMs = d2 - d1;
                return Math.max(1, diffMs / (1000 * 60 * 60 * 24));
            }

            function calculateRoomTotal({ basePrice, nights, numGuests, roomType }) {
                let roomCost = basePrice * nights;
                let nightDiscount = nights > 2 ? roomCost * 0.15 : 0;

                let extraGuests = 0;
                if (roomType === 'Standard Single') extraGuests = Math.max(0, numGuests - 1);
                else if (roomType === 'Standard Double') extraGuests = Math.max(0, numGuests - 2);

                if (roomType === 'Deluxe Single') extraGuests = Math.max(0, numGuests - 1);
                else if (roomType === 'Deluxe Double') extraGuests = Math.max(0, numGuests - 2);

                if (roomType === 'Suite Single') extraGuests = Math.max(0, numGuests - 1);
                else if (roomType === 'Suite Double') extraGuests = Math.max(0, numGuests - 2);

                let guestCharge = basePrice * 0.10 * extraGuests * nights;

                const subtotal = roomCost - nightDiscount + guestCharge;
                const total = subtotal * 1.12; // 12% tax included

                return { roomCost, nightDiscount, guestCharge, subtotal, total };
            }

            function calculateCartSummary(carts) {
                let totalRoomCost = 0, totalNightDiscount = 0, totalGuestCharge = 0, subtotalRooms = 0;

                carts.forEach(cart => {
                    const nights = getNights(cart.CheckInDate, cart.CheckOutDate);
                    const roomData = calculateRoomTotal({
                        basePrice: cart.BasePrice,
                        nights: nights,
                        numGuests: cart.NumAdults,
                        roomType: cart.RoomTypeName
                    });

                    totalRoomCost += roomData.roomCost;
                    totalNightDiscount += roomData.nightDiscount;
                    totalGuestCharge += roomData.guestCharge;
                    subtotalRooms += roomData.subtotal;
                });

                const totalWithTax = subtotalRooms * 1.12;

                return {
                    totalRoomCost,
                    totalNightDiscount,
                    totalGuestCharge,
                    subtotalRooms,
                    totalWithTax
                };
            }

            function renderPriceBreakdown(carts) {
                const summary = calculateCartSummary(carts);
                const breakdownList = $("#price-breakdown-list");
                if (!breakdownList.length) return;

                breakdownList.html(`
                    <div class="flex justify-between">
                        <div class=" justify-center text-black text-base font-normal font-roboto">Subtotal Rooms Cost</div>
                        <div class="  text-black text-base font-normal font-roboto">₱ ${summary.totalRoomCost.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                    </div>

                    <div class="flex justify-between">
                        <div class="  text-black text-base font-normal font-roboto">Night Discount</div>
                        <div class="  text-black text-base font-normal font-roboto">-₱ ${summary.totalNightDiscount.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                    </div>

                    <div class="flex justify-between">
                        <div class="  text-black text-base font-normal font-roboto">Additional Guest Charge</div>
                        <div class="  text-black text-base font-normal font-roboto">₱ ${summary.totalGuestCharge.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                    </div>

                    <div class="flex justify-between">
                        <div class="  text-black text-base font-normal font-roboto">Subtotal (before tax)</div>
                        <div class="  text-black text-base font-normal font-roboto">₱ ${summary.subtotalRooms.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                    </div>

                    <div class="flex justify-between">
                        <div class="  text-black text-base font-normal font-roboto">Tax (12% included in total)</div>
                        <div class="  text-black text-base font-normal font-roboto">₱ ${(summary.totalWithTax - summary.subtotalRooms).toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                    </div>
    
                    <div class="w-full h-0.5 bg-linear-to-r from-yellow-100 to-yellow-700 rounded-[100px] flex  items-center mt-4"></div>

                `);

                $("#cart-total").html(`
                    <div class="flex justify-between font-roboto text-xl font-bold text-black">
                        <div>Total (12% tax included)</div>
                        <div>₱ ${summary.totalWithTax.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                    </div>
                `);
            }

            // Initialize breakdown on page load
            let carts = <?= json_encode($carts) ?>;
            renderPriceBreakdown(carts);

            // ==================== DELETE CART ITEM ====================
            $('.delete-cart-item').click(function () {
                const btn = $(this);
                const cartRoomID = btn.data('cartroomid');

                $.ajax({
                    url: '/cart-remove',
                    type: 'POST',
                    data: { cartRoomID: cartRoomID },
                    success: function (response) {
                        if (response.success) {
                            btn.closest('.flex.gap-8').fadeOut(200, function () {
                                $(this).remove();

                                if (response.cartCount > 0) $('#cart-count').text(response.cartCount).show();
                                else $('#cart-count').hide();

                                // Remove from JS carts array
                                carts = carts.filter(c => c.CartRoomID != cartRoomID);
                                renderPriceBreakdown(carts);

                                if (response.cartCount === 0) {
                                    $('#cart-summary-section').remove();
                                    $('.flex-1').append(`
                            <div class="flex flex-col border rounded p-4 shadow w-full max-w-xl gap-2">
                                <h1 class="font-bold">No booking found in cart</h1>
                                <p class="italic">You have not added any rooms or products to your cart yet.</p>
                            </div>
                        `);
                                }
                            });
                        } else showToast(response.error);
                    },
                    error: function () { showToast('An error occurred while removing the item.'); }
                });
            });

            // ==================== USE ACCOUNT DETAILS ====================
            $('#use-account-details').change(function () {
                if (this.checked) {
                    const userID = $(this).data('user-id');
                    if (!userID) return;

                    $.ajax({
                        url: '/get-profile',
                        type: 'POST',
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                const data = res.data;

                                $('input[name="fname"]').val(data.FirstName || '');
                                $('input[name="lname"]').val(data.LastName || '');
                                $('input[name="email"]').val(data.Email || '');
                                $('#birthDate').flatpickr().setDate(data.BirthDate || '', true);

                                // ================== PHONE ==================
                                let fullPhone = data.PhoneContact || '';
                                fullPhone = fullPhone.replace(/[^0-9+]/g, ''); // clean input

                                let countryCode = '+63'; // default
                                let localNumber = fullPhone;

                                // Match country codes exactly from your dropdown
                                const countryOptions = ['+63', '+1', '+44', '+61'];
                                for (let code of countryOptions) {
                                    if (fullPhone.startsWith(code)) {
                                        countryCode = code;
                                        localNumber = fullPhone.slice(code.length);
                                        break;
                                    }
                                }

                                $('select[name="country_code"]').val(countryCode);
                                $('input[name="phone"]').val(localNumber);

                            } else showToast(res.error, 'error');
                        },
                        error: function () {
                            showToast('Failed to load account details', 'error');
                        }
                    });
                } else {
                    // clear inputs
                    $('input[name="fname"], input[name="lname"], input[name="phone"], input[name="email"]').val('');
                    $('#birthDate').flatpickr().clear();
                    $('select[name="country_code"]').val('+63'); // reset default
                }
            });

            // ==================== FLATPICKR BIRTHDATE ====================
            const birthDatePicker = flatpickr("#birthDate", {
                dateFormat: "Y-m-d",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
                altInput: true,
                altFormat: "F j, Y",
            });

            // ==================== PAYMENT MODAL ====================
            function getPaymentContent(method, totalAmount) {
                switch (method) {
                    case 'Card':
                        return `
            <label class="block">Name on Card
                <input type="text" class="w-full border p-2 rounded" placeholder="John Doe" required>
            </label>
            <label class="block">Card Number
                <input type="text" class="w-full border p-2 rounded" placeholder="1234567812345678" 
                       required pattern="\\d{13,16}" title="Enter 13 to 16 digit card number">
            </label>
            <div class="flex gap-2">
                <label class="flex-1">Expiry Date
                    <input type="text" class="w-full border p-2 rounded" placeholder="MM/YY" 
                           required pattern="^(0[1-9]|1[0-2])\\/\\d{2}$" title="Enter in MM/YY format">
                </label>
                <label class="flex-1">CVV
                    <input type="text" class="w-full border p-2 rounded" placeholder="123" 
                           required pattern="\\d{3,4}" title="Enter 3 or 4 digit CVV">
                </label>
            </div>
        `;
                    case 'Cash':
                        return `
            <p class="text-sm mb-2">Pay in cash at check-in. Provide card for hold.</p>
            <label class="block">Name on Card
                <input type="text" class="w-full border p-2 rounded" placeholder="John Doe" required>
            </label>
            <label class="block">Card Number
                <input type="text" class="w-full border p-2 rounded" placeholder="1234567812345678" 
                       required pattern="\\d{13,16}" title="Enter 13 to 16 digit card number">
            </label>
        `;
                    case 'E-Wallet':
                        return `
            <p class="text-sm mb-2">Scan QR code to pay</p>
            <div class="flex justify-center">
                <img src="/assets/images/qr-code.png" alt="E-Wallet QR" class="w-48 h-48">
            </div>
        `;
                    default:
                        return `<p>No payment method selected.</p>`;
                }
            }

            // Open modal
            $('#to-checkout').click(function () {
                const selectedPayment = $('input[name="payment"]:checked').val();
                if (!selectedPayment) { showToast('Please select a payment method.'); return; }

                const totalAmount = calculateCartSummary(carts).totalWithTax.toFixed(2);
                $('#payment-modal-title').text(selectedPayment + ' Payment');
                $('#payment-modal-content').html(getPaymentContent(selectedPayment, totalAmount));

                // Show modal
                $('#payment-modal').removeClass('opacity-0 pointer-events-none').addClass('opacity-100');
            });

            $('#modal-close').click(function () {
                $('#payment-modal').removeClass('opacity-100').addClass('opacity-0 pointer-events-none');
            });
            $('#modal-pay').click(function () {
                const inputs = $('#payment-modal-content').find('input');
                let valid = true;

                inputs.each(function () {
                    if (!this.checkValidity()) {
                        $(this).addClass('border-red-500'); // optional visual cue
                        valid = false;
                    } else {
                        $(this).removeClass('border-red-500');
                    }
                });

                if (!valid) {
                    showToast('Please fill in all required payment details.', 'error');
                    return;
                }

                $('#reservation-form').submit(); // only submit if valid
            });
            // ==================== RESERVATION SUBMIT ====================
            $("#reservation-form").submit(function (e) {
                e.preventDefault();
                const phoneFull = $("#country_code").val() + $("#phone").val();
                const guestData = {
                    fname: $("#fname").val(),
                    lname: $("#lname").val(),
                    email: $("#email").val(),
                    phone: phoneFull,
                    birthDate: $("#birthDate").val()
                };

                const selectedPayment = $('input[name="payment"]:checked').val();
                if (!selectedPayment) { showToast("Please select a payment method.", "error"); return; }

                $.ajax({
                    url: "/reservation-submit",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ guest: guestData, paymentMethod: selectedPayment, totalAmount: calculateCartSummary(carts).totalWithTax.toFixed(2) }),
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            showToast(response.message, "success");
                            setTimeout(() => window.location.href = "/bookings", 1000);
                        } else showToast(response.error || "Reservation failed.", "error");
                    },
                    error: function (xhr) {
                        showToast(xhr.responseText);
                        showToast('An error occurred during reservation.');
                    }
                });
            });

        });
    </script>
</body>

</html>