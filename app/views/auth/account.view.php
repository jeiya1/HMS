<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
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

    <div class="flex-1 flex py-10 px-30 flex-col gap-5">
        <div class="flex text-black text-base font-normal font-crimson">
            <!-- Home button with SVG -->
            <a href="/home" class="flex items-center border border-neutral-300 px-4 py-1">
                <img src="/assets/icons/home.svg" alt="Home" class="w-4 h-4">
            </a>

            <a href="/profile" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                My Account
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">PROFILE</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>
        <?php if ($logged_in): ?>
            <div class="flex justify-center gap-5 font-roboto">
                <div class="flex flex-col border-[0.3px] border-zinc-300 rounded p-2 w-full">
                    <h1 class="font-bold font-crimson text-3xl">PERSONAL INFORMATION</h1>
                    <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                    <p class="text-neutral-700 font-light font-roboto">Please be sure to update your personal information if
                        it has changed.</p>
                    <form id="update-form" action="/update-submit" method="POST" class="flex justify-between gap-8 mt-4">
                        <div class="flex flex-col gap-1 w-1/4">
                            <label for="fname">First Name* </label>
                            <input type="text" name="fname"
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                            <br>
                            <label for="lname">Last Name* </label>
                            <input type="text" name="lname"
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                            <br>
                            <label for="phone">Phone Contact* </label>
                            <input type="tel" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" name="phone" placeholder="000-000-0000"
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                            <br>
                            <label for="birthDate">Birthdate* </label>
                            <input type="text" id="birthDate" name="birthDate" placeholder="Select your birthdate"
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                            <br>
                            <label for="email">Email* </label>
                            <input type="email" name="email"
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                            <br>
                        </div>
                        <div class="flex flex-col gap-2 w-1/4 mr-50">
                            <label for="passwordc">Current Password*</label>
                            <input type="password" id="passwordc" name="passwordc" autocomplete="new-password"
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" autocomplete="new-password"
                                        class="border border-gray-300 p-2 rounded w-full text-black bg-white password-field">
                                    <span class="absolute right-3 top-3 cursor-pointer toggle-password">
                                        <img src="/assets/icons/eye-off.svg" alt="eye" class="w-5 h-5">
                                    </span>
                                </div>

                                <div id="password-error" class="text-red-600 text-sm min-h-1 pointer-events-none"></div>
                            </div>
                        </div>
                    </form>
                    <div class="flex justify-end mb-5 mr-5">
                        <button type="submit" form="update-form"
                            class="group flex items-center justify-center gap-2 text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 w-30 align-middle cursor-pointer hover:bg-[#3F321F] transition-colors">
                            <p
                                class="transition-all group-hover:text-white group-hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)]">
                                SAVE
                            </p>
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-neutral-700 font-light font-crimson mb-4">
                You must be logged in to view your account.
                <span class="text-yellow-900 text-xs font-normal font-['Roboto']">
                    <a href="/registration" class="hover:underline">Register or Login</a>
                </span> now to make checkout faster and keep track of your orders.
            </div>
        <?php endif; ?>

        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>
    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/flatpickr.js"></script>
</body>

</html>