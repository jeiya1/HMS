<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
</head>

<body class="min-h-screen flex flex-col">
    <?php require_once __DIR__ . '/../components/toast.view.php'; ?>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>


    <div class="flex-1 py-10 px-30 flex flex-col gap-5">
        <div class="justify-start text-black text-lg font-normal font-crimson">
            <a href="/home" class="hover:underline">Home</a> &gt; Cart
        </div>

        <h1 class="font-crimson font-bold text-3xl">YOUR BOOKING CART</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-center gap-5 font-roboto">
            <div class="flex flex-col border rounded p-2 shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] w-full gap-2">
                <h1 class="font-bold">No booking found in cart</h1>
                <p class="italic">You have not added any rooms or products to your carts yet.</p>
            </div>
        </div>

    </div>

    <?php require_once __DIR__ . '/../components/footer.view.php'; ?>
    <script src="/js/signup.js"></script>
</body>

</html>