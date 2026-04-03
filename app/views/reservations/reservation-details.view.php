<?php
$paymentMethodName = [
    "card" => "Debit/Credit Card",
    "online_payment" => "QR-Ph",
    "cash" => "Cash"
];

$statusMapPayment = [
    'pending' => ['label' => 'Pending', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'],
    'completed' => ['label' => 'Successful', 'bg' => 'bg-green-500/25', 'outline' => 'outline-green-500'],
    'failed' => ['label' => 'Failed', 'bg' => 'bg-red-500/25', 'outline' => 'outline-red-500'],
    'refunded' => ['label' => 'Refunded', 'bg' => 'bg-orange-500/25', 'outline' => 'outline-orange-500'],
];

$statusMapRoom = [
    'pending' => ['label' => 'Pending', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'],
    'confirmed' => ['label' => 'Booked', 'bg' => 'bg-green-500/25', 'outline' => 'outline-green-500'],
    'checked_in' => ['label' => 'Checked In', 'bg' => 'bg-blue-500/25', 'outline' => 'outline-blue-500'],
    'checked_out' => ['label' => 'Checked Out', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'],
    'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-red-500/25', 'outline' => 'outline-red-500'],
];
?>
<?php
$subtotalRooms = 0;
$additionalGuestCharge = 0;
$totalNights = 0;
$guestDiscount = 0;
$nightDiscount = 0;

// If $payment exists and has relevant fields from SQL, use it
if (!empty($payment) && isset($payment['TotalBeforeDiscount'], $payment['DiscountAmount'], $payment['Amount'])) {
    $subtotalRooms = $payment['TotalBeforeDiscount'] ?? 0; // 1800
    $guestDiscount = $payment['DiscountAmount'] ?? 0;      // 360
    $additionalGuestCharge = $payment['AdditionalGuestCharge'] ?? 0; // default 0
    $nightDiscount = $payment['NightDiscount'] ?? 0; // default 0

    // Subtotal before tax (sum of room subtotal minus discounts + extra charges)
    $subtotalBeforeTax = $subtotalRooms - $guestDiscount - $nightDiscount + $additionalGuestCharge;

    // Compute tax as 12% of subtotal before tax
    $tax = $subtotalBeforeTax * 0.12;

    // Total = subtotal before tax + tax
    $total = $subtotalBeforeTax + $tax;
} else {
    // Fallback: calculate from $rooms array
    foreach ($rooms as $room) {
        $checkIn = new DateTime($room['CheckInDate']);
        $checkOut = new DateTime($room['CheckOutDate']);
        $nights = $checkOut->diff($checkIn)->days;

        $totalNights += $nights;
        $subtotalRooms += $room['BasePrice'];

        // Determine base occupancy
        $baseOccupancy = stripos($room['RoomType'], 'Single') !== false ? 1 : 2;

        // Extra guest charge: 10% per extra guest
        $extraGuests = max(0, $room['NumAdults'] + $room['NumChildren'] - $baseOccupancy);
        $additionalGuestCharge += $room['BasePrice'] * 0.10 * $extraGuests;

        // Guest discount: 20% per extra guest (or your configured value)
        $guestDiscount += $room['BasePrice'] * 0.20 * $extraGuests;
    }

    // Night discount: 15% off if stay > 3 nights
    $nightDiscount = $totalNights > 3 ? $subtotalRooms * 0.15 : 0;

    // Subtotal before tax
    $subtotalBeforeTax = $subtotalRooms - $nightDiscount - $guestDiscount + $additionalGuestCharge;

    // Tax 12%
    $tax = $subtotalBeforeTax * 0.12;

    // Total
    $total = $subtotalBeforeTax + $tax;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
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

            <a href="/bookings" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1">
                Bookings
            </a>

            <a href="" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Booking Details
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">Booking Details</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="w-full h-11 bg-linear-to-r from-stone-600 to-yellow-100 rounded-sm items-center">
            <div class="items-center flex justify-start text-white font-normal font-roboto text-lg pt-1.5 pl-4">
                Booking Reference <?= htmlspecialchars($guestDetails['BookingToken']); ?> placed on
                <?= htmlspecialchars($guestDetails['BookingDate']); ?>
            </div>
        </div>

        <?php if (!empty($rooms)): ?>
            <div id="room-summary-section" class="flex justify-center gap-5 font-roboto">
                <div class="flex flex-col w-3/5 gap-4">
                    <div id="rooms-section" class="section flex flex-col border-[0.3px] border-zinc-300 rounded">
                        <div class="section-header flex justify-between px-8 py-4">
                            <h1 class="font-semibold font-crimson text-2xl mb-1">Room Details</h1>
                        </div>
                        <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>

                        <!-- CONTENT -->
                        <div class="flex flex-col gap-2 p-8">
                            <div id="room-items-container" class="flex flex-col gap-5 font-roboto mb-3">
                                <?php foreach ($rooms as $index => $room): ?>
                                    <?php
                                    $roomType = $room['RoomType'];
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
                                        <div class="flex-1">
                                            <img src="<?= $imagePath ?>" alt="Room Image" class="w-50 object-cover rounded">
                                        </div>
                                        <div class="flex-5 flex flex-col gap-3">
                                            <h2 class="font-bold font-crimson text-xl">
                                                <?php echo htmlspecialchars($room['RoomType']); ?> Room #
                                                <?php echo htmlspecialchars($room['RoomNumber']); ?>
                                            </h2>
                                            <div class="grid grid-cols-2 grid-rows-3 gap-y-2 gap-x-12 w-full">
                                                <!-- Row 1 -->
                                                <div class="flex justify-between">
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">
                                                        Check-in
                                                    </p>
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">
                                                        <?php echo htmlspecialchars($room['CheckInDate']); ?>
                                                        (12:00 PM)
                                                    </p>
                                                </div>
                                                <div class="flex justify-between">
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">
                                                        Check-in
                                                    </p>
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">
                                                        <?php echo htmlspecialchars($room['CheckOutDate']); ?>
                                                        (11:00 AM)
                                                    </p>
                                                </div>

                                                <!-- Row 2 -->
                                                <div class="flex justify-between">
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">Guests
                                                    </p>
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">
                                                        <?php echo htmlspecialchars($room['NumAdults'] + $room['NumChildren']); ?>
                                                    </p>
                                                </div>
                                                <div class="flex justify-between">
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">Status
                                                    </p>
                                                    <?php
                                                    $roomStatus = $room['RoomStatus']; // e.g., 'pending', 'confirmed', etc.
                                                    $statusData = $statusMapRoom[$roomStatus] ?? ['label' => 'Unknown', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'];
                                                    ?>
                                                    <p
                                                        class="justify-center text-sm font-normal font-roboto <?= $statusData['bg'] ?> <?= $statusData['outline'] ?> rounded px-2 py-1 text-center">
                                                        <?= htmlspecialchars($statusData['label']) ?>
                                                    </p>
                                                </div>
                                                <!-- Row 3 (for future info) -->
                                                <div class="flex justify-between">
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">TOTAL
                                                        PRICE
                                                    </p>
                                                    <p class="justify-center text-black text-sm font-normal font-roboto">
                                                        ₱<?php echo htmlspecialchars($room['BasePrice']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div id="rooms-section" class="section flex flex-col border-[0.3px] border-zinc-300 rounded">
                        <div class="section-header flex justify-between px-8 py-4">
                            <h1 class="font-semibold font-crimson text-2xl mb-1">Hotel Policies</h1>
                        </div>
                        <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>

                        <!-- CONTENT -->
                        <div class="flex flex-col gap-2 p-8">
                            <div id="policies-container" class="flex flex-col gap-5 font-roboto mb-3">

                                <ul class="list-disc text-black text-md font-normal font-roboto ml-5">
                                    <li>Accommodation will only be provided to guests whose details are registered at the
                                        hotel
                                        front
                                        desk.</li>
                                    <li>Guests must present a valid photo identification during check-in.</li>
                                    <li>Applicable taxes and government charges may apply.</li>
                                    <li>Full or advance payment may be required upon check-in.</li>
                                    <li>Standard check-in time is 12:00 PM and check-out time is 11:00 AM. Early check-in
                                        and
                                        late
                                        check-out are subject to availability.</li>
                                    <li>The hotel reserves the right to refuse accommodation to guests who do not comply
                                        with hotel
                                        policies and regulations.</li>
                                    <li>Guests are responsible for any damage or loss caused to hotel property during their
                                        stay.
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="self-start flex flex-col gap-3 w-2/5">
                    <div class="self-start flex flex-col border-[0.3px] border-zinc-300 rounded w-full"
                        id="room-summary-right">
                        <h2 class="font-semibold font-crimson text-xl px-8 py-4">Payment Details</h2>
                        <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg "></div>
                        <div class="text-sm font-roboto px-8 py-4">
                            <div class="flex justify-between">
                                <strong>Payment Method:</strong>
                                <span><?= htmlspecialchars($paymentMethodName[$payment['PaymentMethod']] ?? 'N/A') ?></span>
                            </div>
                            <?php
                            $statusKey = $payment['PaymentStatus'] ?? 'pending';
                            $status = $statusMapPayment[$statusKey] ?? ['label' => 'Unknown', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'];
                            ?>
                            <div class="flex justify-between mt-1">
                                <strong>Payment Status:</strong>
                                <div
                                    class="w-20 h-5 pl-2.5 pr-3 py-[5px] <?= $status['bg'] ?> rounded-sm outline <?= $status['outline'] ?> outline-offset-[-0.50px] inline-flex justify-center items-center gap-2.5 text-xs font-medium">
                                    <?= htmlspecialchars($status['label']) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="self-start flex flex-col border-[0.3px] border-zinc-300 rounded w-full"
                        id="room-summary-right">
                        <h2 class="font-semibold font-crimson text-xl px-8 py-4">Payment Summary</h2>
                        <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                        <div class="text-sm font-roboto px-8 py-4">
                            <div class="flex justify-between"><span>Subtotal Rooms Cost:</span><span>₱
                                    <?= number_format($subtotalRooms, 2) ?></span></div>
                            <div class="flex justify-between"><span>Night Discount:</span><span>-₱
                                    <?= number_format($nightDiscount, 2) ?></span></div>
                            <div class="flex justify-between"><span>Guest Discount:</span><span>-₱
                                    <?= number_format($guestDiscount, 2) ?></span></div>
                            <div class="flex justify-between"><span>Additional Guest Charge:</span><span>₱
                                    <?= number_format($additionalGuestCharge, 2) ?></span></div>
                            <div class="flex justify-between"><span>Subtotal (before tax):</span><span>₱
                                    <?= number_format($subtotalBeforeTax, 2) ?></span></div>
                            <div class="flex justify-between"><span>Tax (12% included in total):</span><span>₱
                                    <?= number_format($tax, 2) ?></span></div>
                            <div class="flex justify-between mt-2 font-semibold">
                                <span>Total (12% tax included):</span><span>₱ <?= number_format($total, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="self-start flex flex-col border-[0.3px] border-zinc-300 rounded w-full"
                        id="room-summary-right">
                        <h2 class="font-semibold font-crimson text-xl px-8 py-4">Guest Details</h2>
                        <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                        <div class="text-sm font-roboto px-8 py-4">
                            <div class="flex justify-between"><strong>Full
                                    Name:</strong><span><?= htmlspecialchars($guestDetails['FullName']); ?></span></div>
                            <div class="flex justify-between">
                                <strong>Email:</strong><span><?= htmlspecialchars($guestDetails['Email']); ?></span>
                            </div>
                            <div class="flex justify-between"><strong>Contact
                                    Number:</strong><span><?= htmlspecialchars($guestDetails['PhoneContact']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col border rounded p-4 shadow w-full max-w-xl gap-2">
                <h1 class="font-bold">No Rooms found in reservation</h1>
                <p class="italic">Something went wrong.</p>
            </div>
        <?php endif; ?>
        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>