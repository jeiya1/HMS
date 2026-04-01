<?php
$paymentMethod = array(
    "card" => "Debit/Credit Card",
    "online_payment" => "QR-Ph",
    "cash" => "Cash"
);
?>
<?php
$statusMap = [
    'pending' => ['label' => 'Pending', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'],
    'confirmed' => ['label' => 'Successful', 'bg' => 'bg-green-500/25', 'outline' => 'outline-green-500'],
    'checked_in' => ['label' => 'Checked_In', 'bg' => 'bg-blue-500/25', 'outline' => 'outline-blue-500'],
    'checked_out' => ['label' => 'Checked_out', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'],
    'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-red-500/25', 'outline' => 'outline-red-500'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
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

            <a href="/profile" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1">
                My Account
            </a>

            <a href="/bookings" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Bookings
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">BOOKINGS</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <?php if ($logged_in): ?>
            <div class="text-neutral-700 text-sm font-light font-roboto">Here are the orders you’ve placed since your
                account was created.</div>
            <table class="w-full font-roboto border-collapse">
                <thead>
                    <tr class="bg-[#FAFAFA] text-black">
                        <th class="py-2 px-4 border border-gray-400 text-left">Order Reference</th>
                        <th class="py-2 px-4 border border-gray-400 text-left">Booking Date</th>
                        <th class="py-2 px-4 border border-gray-400 text-left">Total Price</th>
                        <th class="py-2 px-4 border border-gray-400 text-left">Payment</th>
                        <th class="py-2 px-4 border border-gray-400 text-left">Status</th>
                        <th class="py-2 px-4 border border-gray-400 text-left">Information</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservations)): ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr class="bg-white">
                                <td class="py-2 px-4 border border-gray-400">
                                    <?= htmlspecialchars($reservation['BookingToken']) ?>
                                </td>
                                <td class="py-2 px-4 border border-gray-400">
                                    <?= !empty($reservation['PaymentDate'])
                                        ? date('F j, Y, g:i A', strtotime($reservation['PaymentDate']))
                                        : 'N/A' ?>
                                </td>
                                <td class="py-2 px-4 border border-gray-400">
                                    <?= !empty($reservation['Amount']) ? '₱ ' . number_format($reservation['Amount'], 2) : '0.00' ?>
                                </td>
                                <td class="py-2 px-4 border border-gray-400">
                                    <?= !empty($paymentMethod[$reservation['PaymentMethod']]) ? htmlspecialchars($paymentMethod[$reservation['PaymentMethod']]) : 'N/A' ?>
                                </td>
                                <td class="py-2 px-4 border border-gray-400">
                                    <?php
                                    $statusKey = $reservation['ReservationStatus'] ?? 'pending';
                                    $status = $statusMap[$statusKey] ?? ['label' => 'Unknown', 'color' => 'gray'];
                                    ?>
                                    <div
                                        class="w-20 h-5 pl-2.5 pr-3 py-[5px] <?= $status['bg'] ?> rounded-sm outline <?= $status['outline'] ?> outline-offset-[-0.50px] inline-flex justify-center items-center gap-2.5 text-xs font-medium">
                                        <?= htmlspecialchars($status['label']) ?>
                                    </div>
                                </td>
                                <td class="py-2 px-4 border border-gray-400">
                                    <a href="/reservation/<?= urlencode($reservation['BookingToken']) ?>"
                                        class="text-blue-600 hover:underline">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No reservations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-neutral-700 font-light font-crimson mb-4">
            You must be logged in to view your bookings.
            <span class="text-yellow-900 text-xs font-normal font-['Roboto']">
                <a href="/registration" class="hover:underline">Register or Login</a>
            </span> now to make checkout faster and keep track of your orders.
        </div>
    <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>