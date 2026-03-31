<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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
        <div class="justify-start text-black text-lg font-normal font-crimson">
            <a href="/home" class="hover:underline">Home</a> &gt; Bookings
        </div>

        <h1 class="font-crimson font-bold text-3xl">BOOKINGS</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>


        <div class="text-neutral-700 text-sm font-light font-roboto">Here are the orders you’ve placed since your
            account was created.</div>
        <table class="w-full font-roboto border-collapse">
            <thead>
                <tr class="bg-[#FAFAFA] text-black">
                    <th class="py-2 px-4 border border-gray-400 text-left">Order Reference</th>
                    <th class="py-2 px-4 border border-gray-400 text-left">Date</th>
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
                                <?= !empty($reservation['Amount']) ? '$' . number_format($reservation['Amount'], 2) : '0.00' ?>
                            </td>
                            <td class="py-2 px-4 border border-gray-400">
                                <?= !empty($reservation['PaymentMethod']) ? htmlspecialchars($reservation['PaymentMethod']) : 'N/A' ?>
                            </td>
                            <td class="py-2 px-4 border border-gray-400">
                                <?= htmlspecialchars($reservation['ReservationStatus']) ?>
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

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>