<?php
$paymentMethod = array(
    "card" => "Debit/Credit Card",
    "online_payment" => "QR-Ph",
    "cash" => "Cash"
);
?>
<?php
$statusMapReservation = [
    'pending' => ['label' => 'Pending', 'bg' => 'bg-gray-500/25', 'outline' => 'outline-gray-500'],
    'confirmed' => ['label' => 'Successful', 'bg' => 'bg-green-500/25', 'outline' => 'outline-green-500'],
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
                        <th class="py-2 px-4 border border-gray-400 text-left">Actions</th>
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
                                    $status = $statusMapReservation[$statusKey] ?? ['label' => 'Unknown', 'color' => 'gray'];
                                    ?>
                                    <div
                                        class="w-20 h-5 pl-2.5 pr-3 py-[5px] <?= $status['bg'] ?> rounded-sm outline <?= $status['outline'] ?> outline-offset-[-0.50px] inline-flex justify-center items-center gap-2.5 text-xs font-medium">
                                        <?= htmlspecialchars($status['label']) ?>
                                    </div>
                                </td>
                                <td class="py-2 px-4 border border-gray-400">
                                    <?php if (in_array($reservation['ReservationStatus'], ['pending', 'confirmed'])): ?>
                                        <form method="POST" action="/reservation/cancel"
                                            onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <input type="hidden" name="booking_token"
                                                value="<?= htmlspecialchars($reservation['BookingToken']) ?>">
                                            <button type="button"
                                                onclick="openCancelModal('<?= htmlspecialchars($reservation['BookingToken']) ?>')"
                                                class="text-red-600 hover:underline cursor-pointer">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">—</span>
                                    <?php endif; ?>
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
            You must be logged in to view your bookings. Only bookings made after creating an account are saved.
            <span class="text-yellow-900 text-xs font-normal font-['Roboto']">
                <a href="/registration" class="hover:underline">Register or Login</a>
            </span> now to make checkout faster and keep track of your orders.
        </div>
    <?php endif; ?>
    </div>


    <div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Cancel Booking</h2>
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to cancel this booking?
            </p>

            <div class="flex justify-end gap-3">
                <button onclick="closeCancelModal()"
                    class="px-4 py-1 border border-gray-300 rounded hover:bg-gray-100 cursor-pointer">
                    No
                </button>

                <form id="cancelForm">
                    <input type="hidden" name="booking_token" id="cancelBookingToken">
                    <button type="submit"
                        class="px-4 py-1 bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer">
                        Yes, Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script>
        function openCancelModal(token) {
            $('#cancelBookingToken').val(token);
            $('#cancelModal').removeClass('hidden').addClass('flex');
        }

        function closeCancelModal() {
            $('#cancelModal').addClass('hidden').removeClass('flex');
        }

        // Close modal when clicking outside
        $('#cancelModal').on('click', function (e) {
            if (e.target === this) closeCancelModal();
        });

        // AJAX cancel function
        function confirmCancel(token) {
            $.ajax({
                url: '/reservation/cancel',
                type: 'POST',
                data: { booking_token: token },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showToast('Reservation cancelled.');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        showToast('Error: ' + response.message);
                    }
                },
                error: function (xhr) {
                    showToast('Error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });
        }

        // Submit handler
        $('#cancelForm').on('submit', function (e) {
            e.preventDefault(); // prevent normal form submission
            const token = $('#cancelBookingToken').val();
            confirmCancel(token); // call AJAX
            closeCancelModal(); // optionally close immediately
        });
    </script>
</body>

</html>