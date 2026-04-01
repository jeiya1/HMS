<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
</head>

<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <main class="flex-1 py-10 px-30 flex flex-col">
        <div class="flex text-black text-base font-normal font-crimson">
            <!-- Home button with SVG -->
            <a href="/home" class="flex items-center border border-neutral-300 px-4 py-1">
                <img src="/assets/icons/home.svg" alt="Home" class="w-4 h-4">
            </a>

            <a href="/" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                404
            </a>
        </div>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg mt-5"></div>
        <div class="text-black font-normal font-crimson text-2xl my-3">
            404 - Page Not Found
        </div>

        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>
    </main>

    <footer class="bg-linear-to-r from-black/75 to-stone-500/75 flex justify-center p-6 gap-8 items-center">
        <div class="text-2xl font-crimson italic bg-gradient-to-b from-yellow-100 to-yellow-800 text-transparent bg-clip-text">
            Find your perfect stay
        </div>
        <p class="w-40 justify-center text-yellow-100 text-[10px] font-light font-['Roboto']">
            Comfortable rooms and suites are always ready for your visit.
        </p>
        <button class="w-28 h-9 rounded-[30px] border-[0.50px] border-yellow-100 hover:border-yellow-600 hover:bg-yellow-300 text-yellow-100 hover:text-stone-700">
            <p class="justify-center text-xs font-light font-roboto">Booking</p>
        </button>
    </footer>
</body>

</html>