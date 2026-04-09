<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Reservation</title>
    <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="flex-1 py-10 px-30 flex flex-col gap-5">
        <div class="flex text-black text-base font-normal font-crimson">
            <a href="/home" class="flex items-center border border-neutral-300 px-4 py-1">
                <img src="/assets/icons/home.svg" alt="Home" class="w-4 h-4">
            </a>
            <a href="/bookings" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1">
                Bookings
            </a>
            <span class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Cancel Reservation
            </span>
        </div>

        <h1 class="font-crimson font-bold text-3xl">CANCEL RESERVATION</h1>
        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex flex-col border-[0.3px] rounded p-5 w-1/3 gap-4 border-neutral-300">
            <p class="italic">Are you sure you want to cancel your reservation?</p>

            <form id="guestCancelForm" method="POST">
                <input type="hidden" id="bookingToken" name="booking_token"
                    value="<?= htmlspecialchars($reservation['BookingToken']) ?>">

                <button type="submit"
                    class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 cursor-pointer shadow-2xl transition-colors hover:bg-[#3F321F] hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)]">
                    Confirm Cancel
                </button>
            </form>
        </div>
        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>

    <script>
        $(document).ready(function () {
            // Back button
            $('#cancelBtn').on('click', function () {
                window.location.href = '/bookings';
            });

            // Guest cancel AJAX
            $('#guestCancelForm').on('submit', function (e) {
                e.preventDefault();
                const token = $('#bookingToken').val();
                console.log('Sending guest cancel request for token:', token);

                $.ajax({
                    url: '/reservation/cancel/guest',
                    type: 'POST',
                    data: { booking_token: token },
                    dataType: 'json',
                    success: function (response) {
                        console.log('Guest cancel response:', response);
                        if (response.success) {
                            showToast(response.message || 'Reservation cancelled.', 'success');
                            setTimeout(() => window.location.href = '/bookings', 1000);
                        } else {
                            showToast(response.message || 'Failed to cancel reservation.', 'error');
                        }
                    },
                    error: function (xhr) {
                        console.error('AJAX error:', xhr);
                        showToast('Error cancelling reservation.', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>