<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions</title>
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

            <!-- Cart button with text -->
            <a href="/registration" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1">
                Authentication
            </a>

            <a href="/terms" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Terms & Conditions
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">TERMS & CONDITIONS</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <p class="mt-4 text-gray-700 font-poppins">
            By using this website, you agree to the following terms and conditions. Please read them carefully.
        </p>

        <h2 class="font-bold mt-4">1. Use of Website</h2>
        <p class="text-gray-700 font-poppins">
            You may use this website for lawful purposes only. You must not use it in any way that could damage or
            impair the site or its services.
        </p>

        <h2 class="font-bold mt-4">2. Account Registration</h2>
        <p class="text-gray-700 font-poppins">
            Users must provide accurate information when creating an account. You are responsible for maintaining the
            confidentiality of your login details.
        </p>

        <h2 class="font-bold mt-4">3. Intellectual Property</h2>
        <p class="text-gray-700 font-poppins">
            All content on this website, including text, images, and logos, is the property of the site and protected by
            copyright laws.
        </p>

        <h2 class="font-bold mt-4">4. Limitation of Liability</h2>
        <p class="text-gray-700 font-poppins">
            We are not liable for any damages arising from your use of the website, including loss of data or business
            interruption.
        </p>

        <h2 class="font-bold mt-4">5. Changes to Terms</h2>
        <p class="text-gray-700 font-poppins">
            We may update these Terms & Conditions at any time. Your continued use of the website indicates acceptance
            of the new terms.
        </p>

        <p class="mt-4 text-gray-700 font-poppins">
            For questions, contact us at <a href="mailto:subic.rivera@hotelsubic.com"
                class="underline hover:text-gray-800">support@example.com</a>.
        </p>

        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>