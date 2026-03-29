<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="py-10 px-30 flex flex-col gap-5">
        <div class="justify-start text-black text-lg font-normal font-crimson">
            <a href="/home" class="hover:underline">Home</a> &gt; Profile
        </div>

        <h1 class="font-crimson font-bold text-3xl">PROFILE</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <div class="flex flex-col border-[0.3px] border-zinc-300 rounded p-2 w-full">
                <h1 class="font-bold font-crimson text-3xl">PERSONAL INFORMATION</h1>
                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-600 rounded-lg"></div>
                <p class="text-neutral-700 font-light font-roboto">Please be sure to update your personal information if
                    it has changed.</p>
                <form id="update-form" action="/update-submit" method="POST" class="flex justify-between gap-8">
                    <div class="flex flex-col gap-1 w-full">
                        <label for="fname">First Name: </label>
                        <input type="text" name="fname"
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                        <br>
                        <label for="lname">Last Name: </label>
                        <input type="text" name="lname"
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                        <br>
                        <label for="phone">Phone Contact: </label>
                        <input type="tel" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" name="phone" placeholder="000-000-0000"
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                        <br>
                        <label for="birthDate">Birthday: </label>
                        <input type="text" id="birthDate" name="birthDate" placeholder="Select your birthdate"
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                        <br>
                        <label for="email">Email: </label>
                        <input type="email" name="email"
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                        <br>
                    </div>
                    <div class="flex flex-col gap-2 w-full">
                        <label for="password">Current Password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" autocomplete="new-password" required
                                class="border border-gray-300 p-2 rounded w-full text-black bg-white">

                            <div id="password-error" class="text-red-600 text-sm min-h-1 pointer-events-none"></div>
                        </div>
                        <label for="password">Confirm New Password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required
                            class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    </div>
                </form>

                <input type="submit" value="Save"
                    class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3">
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/signup.js"></script>
    <script src="/js/flatpickr.js"></script>
</body>

</html>