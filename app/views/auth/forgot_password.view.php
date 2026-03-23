<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
</head>

<body>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="py-10 px-30 flex flex-col gap-5">

        <h1 class="font-crimson font-bold text-3xl">ACCOUNT RECOVERY</h1>

        <div class="mx-auto h-1 w-full bg-yellow-900/60 rounded-lg"></div>

        <div class="flex justify-start gap-5 font-roboto">
            <div class="flex flex-col border rounded p-2 shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] w-1/3 gap-2">
                <h1 class="font-bold">PASSWORD RECOVERY</h1>
                <p class="italic">Enter your email to receive a recovery link.</p>

                <form id="recovery-form" action="/forgot-password" method="POST">
                    <label for="email">Email: </label>
                    <input type="email" name="email" required
                        class="border border-gray-300 p-2 rounded w-full text-black bg-white">
                    <br>
                    <br>
                    <input type="submit" value="Send Recovery Link"
                        class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#C39C4D] p-3">
                </form>
            </div>
        </div>

    </div>
    <script src="/js/signup.js"></script>
</body>

</html>