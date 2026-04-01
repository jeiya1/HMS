<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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
        <div class="flex text-black text-base font-normal font-crimson">
            <!-- Home button with SVG -->
            <a href="/home" class="flex items-center border border-neutral-300 px-4 py-1">
                <img src="/assets/icons/home.svg" alt="Home" class="w-4 h-4">
            </a>

            <a href="/registration"
                class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Authentication
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">AUTHENTICATION</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <div class="flex flex-col border border-zinc-300 rounded p-2 w-full gap-2">
                <h1 class="font-bold">CREATE AN ACCOUNT</h1>
                <p class="italic">Please enter your details to create an account.</p>
                <form id="signup-form" action="/signup-submit" method="POST">
                    <label for="fname">First Name: </label>
                    <input type="text" name="fname" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <label for="lname">Last Name: </label>
                    <input type="text" name="lname" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <label>Phone Contact:</label>

                    <div class="flex gap-2">
                        <!-- Country Code -->
                        <select name="country_code" required
                            class="border border-gray-300 p-2 rounded bg-white text-black w-28">
                            <option value="+63" selected>🇵🇭 +63</option>
                            <option value="+1">🇺🇸 +1</option>
                            <option value="+44">🇬🇧 +44</option>
                            <option value="+61">🇦🇺 +61</option>
                        </select>

                        <!-- Local Number -->
                        <input type="tel" name="phone" placeholder="9123456789" pattern="[0-9]{7,12}" required
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    </div>
                    <label for="birthDate">Birthday: </label>
                    <input type="text" id="birthDate" name="birthDate" placeholder="Select your birthdate" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <label for="email">Email: </label>
                    <input type="email" name="email" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>

                    <div class="relative">
                        <label for="password">Create Password</label>
                        <input type="password" name="password" autocomplete="new-password" required
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white password-field">
                        <span class="absolute right-3 top-9 cursor-pointer toggle-password">
                            <img src="/assets/icons/eye-off.svg" alt="eye" class="w-5 h-5">
                        </span>
                        <div id="password-error" class="text-red-600 text-sm min-h-1 pointer-events-none"></div>
                    </div>


                    <!-- Terms & Privacy Links -->
                    <p class="text-sm text-gray-600 mt-2">
                        By signing up, you agree to our
                        <a href="/terms" class="underline hover:text-gray-800">Terms & Conditions</a> and
                        <a href="/privacy" class="underline hover:text-gray-800">Privacy Policy</a>.
                    </p>

                    <br>
                    <input type="submit" value="SIGNUP"
                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] hover:bg-[#3F321F] transition-colors cursor-pointer">
                </form>
            </div>

            <div class="flex flex-col border rounded p-2 border-zinc-300 w-full">
                <h1 class="font-bold">ALREADY REGISTERED?</h1>
                <form id="loginForm" action="/login-submit" method="POST">
                    <label for="email">Email: </label>
                    <input type="email" name="email" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>

                    <div class="relative">
                        <label for="password">Password: </label>
                        <input type="password" name="password" autocomplete="current-password" required
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white password-field">
                        <span class="absolute right-3 top-9 cursor-pointer toggle-password">
                            <img src="/assets/icons/eye-off.svg" alt="eye" class="w-5 h-5">
                        </span>
                    </div>
                    <br>
                    <a href="/forgot-password" class="text-gray-500 underline text-sm hover:text-gray-700">
                        Forgot Password?
                    </a>
                    <br>
                    <br>
                    <input type="submit" value="LOGIN"
                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] hover:bg-[#3F321F] transition-colors cursor-pointer">
                </form>
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/flatpickr.js"></script>
</body>

</html>