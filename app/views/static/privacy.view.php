<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
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

            <a href="/privacy" class="flex items-center border border-neutral-300 border-l-0 px-4 py-1 bg-[#F6F6F6]">
                Privacy Policy
            </a>
        </div>

        <h1 class="font-crimson font-bold text-3xl">PRIVACY POLICY</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <p class="mt-4 text-gray-700 font-poppins">
            Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal
            information.
        </p>

        <h2 class="font-bold mt-4">1. Information We Collect</h2>
        <p class="text-gray-700 font-poppins">
            We may collect personal information such as your name, email address, phone number, birthdate, and payment information
            when you use our services.
        </p>

        <h2 class="font-bold mt-4">2. How We Use Your Information</h2>
        <p class="text-gray-700 font-poppins">
            Your information is used to provide and improve our services, communicate with you, and for security
            purposes.
        </p>

        <h2 class="font-bold mt-4">3. Data Protection</h2>
        <p class="text-gray-700 font-poppins">
            We take reasonable measures to protect your personal data. However, we cannot guarantee absolute security.
        </p>

        <h2 class="font-bold mt-4">4. Third-Party Services</h2>
        <p class="text-gray-700 font-poppins">
            We may share information with trusted third parties to provide services or comply with legal obligations.
        </p>

        <h2 class="font-bold mt-4">5. Changes to This Policy</h2>
        <p class="text-gray-700 font-poppins">
            We may update this Privacy Policy from time to time. Changes will be posted on this page.
        </p>

        <p class="mt-4 text-gray-700 font-poppins">
            If you have any questions, please contact us at <a href="mailto:subic.rivera@hotelsubic.com"
                class="underline hover:text-gray-800">support@example.com</a>.
        </p>

        <?php require_once __DIR__ . '/../components/backButton.view.php'; ?>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
</body>

</html>