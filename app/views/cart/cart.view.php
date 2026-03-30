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
            <a href="/home" class="hover:underline">Home</a> &gt; Cart
        </div>

        <h1 class="font-crimson font-bold text-3xl">YOUR BOOKING CART</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <?php if (!empty($carts)): ?>
                <?php foreach ($carts as $cart): ?>
                    <div class="flex flex-col border rounded p-2 shadow w-full gap-2">
                        <h2 class="font-bold"><?php echo htmlspecialchars($cart['RoomTypeName']); ?></h2>
                        <p>Room #: <?php echo htmlspecialchars($cart['RoomNumber']); ?></p>
                        <p>Price: $<?php echo htmlspecialchars($cart['BasePrice']); ?></p>
                        <p>Guests: <?php echo htmlspecialchars($cart['NumAdults']); ?></p>
                        <p>Check-in: <?php echo htmlspecialchars($cart['CheckInDate']); ?></p>
                        <p>Check-out: <?php echo htmlspecialchars($cart['CheckOutDate']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="flex flex-col border rounded p-2 shadow w-full gap-2">
                    <h1 class="font-bold">No booking found in cart</h1>
                    <p class="italic">You have not added any rooms or products to your cart yet.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>