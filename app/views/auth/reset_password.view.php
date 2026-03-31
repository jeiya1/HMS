<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="py-10 px-30 flex flex-col gap-5">
        <h1 class="font-bold text-3xl">Reset Your Password</h1>
        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-start gap-5">
            <div class="flex flex-col border-[0.3px] rounded p-4 border-neutral-300 w-1/3 gap-2">
                <p class="italic">Enter your new password below.</p>

                <form id="reset-form" action="/reset-submit?token=<?= htmlspecialchars($token) ?>" method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                    <label for="password">New Password:</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="border-[0.3px] border-neutral-300 p-2 rounded w-full mb-2 password-field">
                        <span class="absolute right-3 top-3 cursor-pointer toggle-password">
                            <img src="/assets/icons/eye-off.svg" alt="eye" class="w-5 h-5">
                        </span>
                    </div>

                    <label for="passwordr">Confirm Password:</label>
                    <div class="relative">
                        <input type="password" id="passwordr" name="passwordr" required
                            class="border-[0.3px] border-neutral-300 p-2 rounded w-full mb-2 password-field">
                        <span class="absolute right-3 top-3 cursor-pointer toggle-password">
                            <img src="/assets/icons/eye-off.svg" alt="eye" class="w-5 h-5">
                        </span>
                    </div>

                    <div id="password-error" class="error-message mb-2 text-red-600"></div>

                    <input type="submit" value="Reset Password" class="bg-[#C39C4D] text-white p-3 rounded mt-2">
                </form>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>

</body>

</html>