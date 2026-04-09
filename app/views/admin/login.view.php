<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="icon" type="image/x-icon" href="/admin/assets/icons/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/admin/css/output.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <style>
    /* Solid blue border outside */
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 35px solid #111827;
      pointer-events: none;
      z-index: 999;
    }

    /* Green wave border inside */
    body::after {
      content: "";
      position: fixed;
      top: 10px;
      /* sits inside the blue frame */
      left: 10px;
      width: calc(100% - 20px);
      height: calc(100% - 20px);
      border: 52px solid #4B5563;
      pointer-events: none;
      z-index: 998;
      animation: wave 2s infinite ease-in-out;
      box-shadow: 0 0 20px black, 0 0 40px black;
      /* glow + nerdy vibe */
    }

    @keyframes wave {
      0% {
        transform: scale(1);
        opacity: 1;
      }

      25% {
        transform: scale(1.02);
        opacity: 0.9;
      }

      50% {
        transform: scale(1.05);
        opacity: 0.8;
      }

      75% {
        transform: scale(1.02);
        opacity: 0.9;
      }

      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>
  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-200">
  <?php require_once __DIR__ . '/components/toast.view.php'; ?>

  <div class="flex flex-col items-center justify-center">

    <!-- Logo -->
    <div class="transform -translate-y-[40px]">
      <img src="/assets/images/logo.jpg" class="w-90 h-90">
    </div>

    <!-- Login Form Container -->
    <div class="bg-gray-300 shadow-2xl rounded-3xl p-8 w-[550px] h-[350px] 
              flex flex-col items-center justify-center  transform -translate-y-[100px] border border-[#111827]">

      <!-- Label -->
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Hello Admin!</h2>

      <form id="admin-login-form" class="w-full flex flex-col space-y-4">
        <div class="flex flex-col space-y-2">
          <div class="flex items-center border rounded px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
            <ion-icon name="person" class="text-2xl text-gray-500 mr-2"></ion-icon>
            <input id="email-input" name="email" type="email" placeholder="Enter Email"
              class="w-full focus:outline-none">
          </div>
        </div>

        <div class="flex items-center border rounded px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
          <ion-icon name="lock-closed" class="text-2xl text-gray-500 mr-2"></ion-icon>
          <input id="password-input" name="password" type="password" placeholder="Enter Password"
            class="w-full focus:outline-none">
        </div>

        <button type="submit"
          class="bg-gray-800 text-white font-semibold py-2 rounded w-full hover:bg-gray-600 transition cursor-pointer">
          Login
        </button>
      </form>
    </div>
  </div>
  <script>

    $("#admin-login-form").submit(function (e) {
      e.preventDefault();
      $.ajax({
        url: "/admin/login",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
          if (!response.success) {
            showToast(response.error || "Invalid credentials", "error");
          } else {
            window.location.href = response.redirect || "/admin/dashboard";
          }
        },
        error: function (xhr) {
          let message = "Server error. Try again.";
          if (xhr.responseText) {
            try {
              const res = JSON.parse(xhr.responseText);
              if (res.error) message = res.error;
            } catch (e) { }
          }
          showToast(message, "error");
        }
      });
    });
  </script>
</body>

</html>