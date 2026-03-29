<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
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
        <div class="justify-start text-black text-lg font-normal font-crimson">
            <a href="/home" class="hover:underline">Home</a> &gt; Authentication
        </div>

        <h1 class="font-crimson font-bold text-3xl">AUTHENTICATION</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <div class="flex flex-col border rounded p-2 shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] w-full gap-2">
                <!-- Pop up for checking if an account already exists with the email, if not create an account and log in, if yes, log in and redirect to home page -->
                <h1 class="font-bold">CREATE AN ACCOUNT</h1>
                <!-- TODO: Add email verification when signing up -->
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
                    <label for="phone">Phone Contact: </label>
                    <input type="tel" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" name="phone" placeholder="000-000-0000"
                        required class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <label for="birthDate">Birthday: </label>
                    <input type="text" id="birthDate" name="birthDate" placeholder="Select your birthdate" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <label for="email">Email: </label>
                    <input type="email" name="email" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <div class="form-group">
                        <label for="password">Create Password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">

                        <div id="password-error" class="text-red-600 text-sm min-h-1 pointer-events-none"></div>
                    </div>

                    <!-- Terms & Privacy Links -->
                    <p class="text-sm text-gray-600 mt-2">
                        By signing up, you agree to our
                        <a href="/terms" class="underline hover:text-gray-800">Terms & Conditions</a> and
                        <a href="/privacy" class="underline hover:text-gray-800">Privacy Policy</a>.
                    </p>

                    <br>
                    <input type="submit" value="Signup"
                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] hover:bg-[#3F321F] transition-colors">
                </form>
            </div>

            <div class="flex flex-col border rounded p-2 shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] w-full">
                <h1 class="font-bold">ALREADY REGISTERED?</h1>
                <form id="loginForm" action="/login-submit" method="POST">
                    <label for="email">Email: </label>
                    <input type="email" name="email" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <label for="password">Password: </label>
                    <input type="password" name="password" autocomplete="current-password" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>


                    <br>
                    <a href="/forgot-password" class="text-gray-500 underline text-sm hover:text-gray-700">
                        Forgot Password?
                    </a>
                    <br>
                    <br>
                    <input type="submit" value="Login"
                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3 hover:text-white hover:[text-shadow:0_0_8px_rgba(255,255,255,0.9)] hover:bg-[#3F321F] transition-colors">
                </form>
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/flatpickr.js"></script>
</body>

</html>