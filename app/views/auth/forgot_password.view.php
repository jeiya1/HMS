<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
        <div class="justify-start text-black text-lg font-normal font-crimson">
            <a href="/home" class="hover:underline">Home</a> <a href="/signup" class="hover:underline">&gt;
                Authentication</a> &gt; Account Recovery
        </div>

        <h1 class="font-crimson font-bold text-3xl">ACCOUNT RECOVERY</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-start gap-5 font-roboto">
            <div class="flex flex-col border-[0.3px] rounded p-2 w-1/3 gap-2">
                <h1 class="font-bold">PASSWORD RECOVERY</h1>
                <p class="italic">Enter your email to receive a recovery link.</p>

                <form id="recovery-form" action="/forgot-password" method="POST">
                    <label for="email">Email: </label>
                    <input type="email" name="email" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <br>
                    <!-- TODO: add a wait for .. message since creating the recovery link takes a while
                    TODO: add hover animation to button -->
                    <input type="submit" value="Send Recovery Link"
                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3">
                </form>
            </div>
        </div>
        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>

    </div>
    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>