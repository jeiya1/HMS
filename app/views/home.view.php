<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Rivera</title>
  <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/output.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
  <?php require_once __DIR__ . '/components/toast.view.php'; ?>
  <!-- Header -->
  <nav
    class="fixed top-0 z-50 bg-[rgba(0,0,0,0.40)] flex justify-end items-center gap-10 pr-16 p-4 text-white font-crimson text-[20px] font-normal leading-normal w-full">

    <a href="/home" class="relative group">
      <span
        class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
        Home
      </span>
    </a>
    <a href="#gallery" class="relative group">
      <span
        class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
        Gallery
      </span>
    </a>
    <a href="#amenities" class="relative group">
      <span
        class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
        Amenities
      </span>
    </a>
    <a href="#rooms" class="relative group">
      <span
        class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
        Rooms
      </span>
    </a>
    <a href="#about-contact" class="relative group">
      <span
        class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
        About
      </span>
    </a>
    <a href="#about-contact" class="relative group">
      <span
        class="after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-500 group-hover:after:w-full">
        Contact us
      </span>
    </a>
    <?php require_once __DIR__ . '/components/profile.view.php'; ?>
    <a href="/cart" class="relative inline-block">
      <img src="/assets/icons/cart.svg" alt="Cart icon" class="h-8 w-8 transition-all duration-200 rounded 
        hover:drop-shadow-[0_0_10px_rgba(255,255,255,0.9)] hover:scale-105">

      <?php if (!empty($cartCount) && $cartCount > 0): ?>
        <span class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/3 
                     bg-[#FCEDB5] text-black font-crimson text-xs font-bold 
                     w-4 h-4 flex items-center justify-center 
                     border border-[#FCEDB5]">
          <?= $cartCount ?>
        </span>
      <?php endif; ?>
    </a>
  </nav>

  <!-- Header -->
  <div
    class="bg-[url(/assets/images/homebgenhanced.jpg)] bg-cover bg-center min-h-screen flex flex-col justify-between">
    <div class="flex justify-center items-center flex-1">
      <div class="relative w-max mx-auto -mt-30">
        <img src="/assets/images/logo.jpg" alt="logo" class="w-95 relative z-10">
        <div class="absolute inset-0 z-0 rounded-full"
          style="background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 70%); filter: blur(50px);">
        </div>
      </div>
    </div>

    <footer class="bg-[rgba(0,0,0,0.40)] flex justify-center p-10 gap-8 items-center">
      <form method="POST" action="/search">
        <div class="flex gap-2 p-5">

          <!-- Check-In / Check-Out -->
          <div class="relative">
            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
              <img src="/assets/icons/calendar.svg" alt="calendar">
            </span>
            <input type="text" name="checkin" id="daterange" placeholder="Check-In — Check-Out" required
              class="bg-white rounded-sm p-2 pl-9 text-crimson-600 font-crimson border border-gray-300 cursor-pointer"
              value="<?= isset($_POST['checkin']) ? htmlspecialchars($_POST['checkin']) : '' ?>">
          </div>

          <!-- Adults -->
          <div class="relative">
            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
              <img src="/assets/icons/people.svg" alt="people">
            </span>
            <input type="number" id="adults" name="adults" placeholder="No. of Adults" min="0"
              class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 "
              value="<?= !empty($_POST['adults']) ? (int) $_POST['adults'] : '' ?>">
          </div>

          <!-- Children -->
          <div class="relative">
            <span class="absolute left-2 top-[13.5px] transform text-gray-400">
              <img src="/assets/icons/people.svg" alt="people">
            </span>
            <input type="number" id="children" name="children" placeholder="No. of Children" min="0"
              class="bg-white rounded-sm pl-9 p-2 text-crimson-600 font-crimson border border-gray-300 "
              value="<?= !empty($_POST['children']) ? (int) $_POST['children'] : '' ?>">
          </div>

          <!-- Submit -->
          <!-- TODO: hover -->
          <div class="flex items-center">
            <button type="submit"
              class="text-white font-roboto text-[14px] font-semibold leading-normal rounded-sm bg-[#714623] p-2.5 cursor-pointer hover:bg-[#654022] transition-colors duration-300">
              SEARCH ROOMS
            </button>
          </div>
        </div>
      </form>
    </footer>

    <!-- Hidden Rooms -->
    <div class="fixed flex inset-0 bg-[rgba(0,0,0,0.7)] items-center justify-center hidden z-50" id="RoomsPopup">
      <div class="flex flex-col p-10 bg-[#EEE2CB] rounded-2xl">
        <h1 class="text-center justify-start text-yellow-900 text-lg font-normal font-crimson">Room
          Selection</h1>
        <button id="closeRooms" class="mt-4 px-4 py-2 bg-yellow-900 text-white rounded font-crimson">
          Button
        </button>
      </div>
    </div>
  </div>

  <!-- Facilities -->
  <div class="bg-[#F9F5ED] flex flex-col justify-center gap-5 pb-5 pt-25" id="gallery">
    <div class="flex flex-col items-center gap-4">
      <h1 class="self-stretch text-center justify-start text-yellow-900 text-5xl font-normal font-crimson">Explore The
        Interiors</h1>
      <div class="text-center text-neutral-700 text-xl font-light font-roboto mr-90 ml-90">Experience the comfort and
        elegance of our hotel interiors. Each space is thoughtfully designed to provide a relaxing and welcoming
        atmosphere for every guest.</div>
      <div class="h-1 w-50 bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
    </div>

    <div class="m-20 relative">
      <!-- Left Button -->
      <div
        class="w-12 h-24 absolute left-0 top-1/2 -translate-y-1/2 cursor-pointer z-10 flex items-center justify-center">
        <div class="arrow-bg w-12 h-24 bg-zinc-300/90 absolute left-0 top-0 rounded-xs"></div>
        <img src="/assets/icons/left-arrow.svg" alt="Left arrow" class="relative z-20 w-3 h-5">
      </div>

      <!-- Right Button -->
      <div
        class="w-12 h-24 right-0 absolute top-1/2 -translate-y-1/2 cursor-pointer z-10 flex items-center justify-center">
        <div class="arrow-bg w-12 h-24 bg-zinc-300/90 absolute left-0 top-0 rounded-xs"></div>
        <img src="/assets/icons/right-arrow.svg" alt="Right arrow" class="relative z-20 w-3 h-5">
      </div>

      <!-- Gallery -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g1.jpg" alt="Spiral staircase" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g2.jpg" alt="Reception area" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g3.jpg" alt="Guest room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g4.jpg" alt="Hallway" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g5.jpg" alt="Poolside area" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g6.jpg" alt="Seating area" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g7.jpg" alt="Bathroom" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g9.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>

        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g9.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>

        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g10.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g11.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g12.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g13.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g14.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g15.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/g16.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>
      </div>
    </div>
  </div>

  <!-- Amenities -->
  <div class="flex flex-col justify-center gap-5 pb-5 pt-25" id="amenities">
    <div class="flex flex-col items-center gap-4">
      <h1 class="self-stretch text-center justify-start text-yellow-900 text-5xl font-normal font-crimson">Amenities
      </h1>
      <p class="text-center text-neutral-700 text-xl font-light font-roboto mr-90 ml-90">Enjoy a range of thoughtfully
        designed amenities that provide comfort, convenience, and relaxation throughout your stay.</p>
      <div class="h-1 w-50 bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
    </div>

    <div class="m-20 relative">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/a1.jpg" alt="Spiral staircase" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64 flex flex-col items-center justify-center text-center p-4">
          <h1 class="text-[#C39C4D] text-3xl font-normal font-crimson mb-2">
            Luxurious Rooms
          </h1>
          <p class="text-neutral-700 text-xl font-light font-roboto leading-6">
            Experience unmatched comfort in our elegantly designed rooms, featuring modern furnishings and a relaxing
            atmosphere perfect for your stay.
          </p>
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/a2.jpg" alt="Guest room" class="w-full h-full object-cover">
        </div>
        <div
          class="overflow-hidden rounded-sm h-64 flex flex-col items-center justify-center text-center p-4 bg-gradient-to-r from-yellow-100/70 to-yellow-900/70 text-white">
          <h1 class="text-3xl font-normal mb-2 font-crimson">Relaxing Lounge Areas</h1>
          <p class="text-neutral-700 text-xl font-light font-roboto leading-6">
            Unwind in our beautifully designed lounge spaces, perfect for relaxing, socializing, or enjoying quiet
            moments.
          </p>
        </div>
        <div
          class="overflow-hidden rounded-sm h-64 flex flex-col items-center justify-center text-center p-4 bg-gradient-to-r from-yellow-900/70 to-yellow-100/70 text-white">
          <h1 class="text-3xl font-normal mb-2 font-crimson">Gym</h1>
          <p class="text-neutral-700 text-xl font-light font-roboto leading-6">
            Maintain your fitness routine with access to our well-equipped gym, designed to provide a convenient and
            energizing workout experience.
          </p>
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/a3.jpg" alt="Seating area" class="w-full h-full object-cover">
        </div>
        <div class="overflow-hidden rounded-sm h-64 flex flex-col items-center justify-center text-center p-4">
          <h1 class="text-[#C39C4D] text-3xl font-normal font-crimson mb-2">
            Outdoor Swimming Pool
          </h1>
          <p class="text-neutral-700 text-xl font-light font-roboto leading-6">
            Take a refreshing dip in our outdoor swimming pool, the perfect place to relax, cool off, and enjoy a
            peaceful atmosphere.
          </p>
        </div>
        <div class="overflow-hidden rounded-sm h-64">
          <img src="/assets/images/a4.jpg" alt="Suite room" class="w-full h-full object-cover">
        </div>

      </div>
    </div>

    <div class="mx-auto h-0.5 w-[calc(100%-5rem)] bg-gradient-to-r from-yellow-100/70 to-yellow-900/70 rounded-lg">
    </div>

    <!-- Our Rooms -->
    <div class="flex flex-col justify-center pb-5 pt-25" id="rooms">
      <div class="flex flex-col items-center gap-4">
        <h1 class="self-stretch text-center justify-start text-yellow-900 text-5xl font-normal font-crimson">Our Rooms
        </h1>
        <p class="text-center text-neutral-700 text-xl font-light font-roboto mr-90 ml-90">
          Discover our comfortable and elegantly designed rooms, created to provide a relaxing and enjoyable stay for
          every guest.</p>
        <div class="h-1 w-50 bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
      </div>
    </div>
    <div>

      <div class="flex flex-wrap justify-center gap-4 w-full m-w-8xl bg-[#EEE2CB] p-14 mt-8">
        <div class="relative w-2/3 md:w-1/2 lg:w-1/3 overflow-hidden rounded-xs">

          <img src="/assets/images/standard.jpg" alt="standard room" class="w-full h-full object-cover">
          <div class="absolute bottom-0 p-2 w-full text-white text-xl font-bold
                  bg-[linear-gradient(90deg,rgba(252,237,181,0.5)_0%,rgba(113,70,35,0.5)_100%)]">
            <div class="flex flex-col gap-1">
              <div class="flex justify-between">

                <h1 class="text-stone-800 text-md font-crimson font-normal">Standard</h1>
                <p class="w-56 text-yellow-100 text-xl font-crimson font-normal">from ₱ 1,800.00 / per night</p>
              </div>
              <p class="text-white text-sm font-light font-roboto [text-shadow:0px_1px_1px_rgb(0_0_0/0.25)]">
                A comfortable and cozy room designed for guests seeking a simple yet relaxing stay, complete with
                essential amenities.</p>
              <a href="/search?room_type=standard&auto=1">
                <button
                  class="self-start text-stone-800 border border-stone-800 px-2 py-1 rounded font-normal font-roboto transition duration-300 ease-in-out transform hover:bg-stone-800 hover:text-white hover:shadow-lg hover:-translate-y-0.5 cursor-pointer">
                  Book Now
                </button>
              </a>
            </div>
          </div>
        </div>
        <div class="relative w-2/3 md:w-1/2 lg:w-1/3 overflow-hidden rounded-xs">
          <img src="/assets/images/deluxe.jpg" alt="deluxe room" class="w-full h-full object-cover">
          <div class="absolute bottom-0 p-2 w-full text-white text-xl font-bold
                  bg-[linear-gradient(90deg,rgba(252,237,181,0.5)_0%,rgba(113,70,35,0.5)_100%)]">
            <div class="flex flex-col gap-1">
              <div class="flex justify-between">

                <!-- TODO: Add deluxe -->
                <h1 class="text-stone-800 text-md font-crimson font-normal">Deluxe</h1>
                <p class="w-56 text-yellow-100 text-xl font-crimson font-normal">from ₱ 2,300.00 / per night</p>
              </div>
              <p class="text-white text-sm font-light font-roboto [text-shadow:0px_1px_1px_rgb(0_0_0/0.25)]">
                Enjoy extra space and enhanced comfort in our deluxe rooms, featuring stylish interiors and upgraded
                amenities.
              </p>
              <a href="/search?room_type=deluxe&auto=1">
                <button
                  class="self-start text-stone-800 border border-stone-800 px-2 py-1 rounded font-normal font-roboto transition duration-300 ease-in-out transform hover:bg-stone-800 hover:text-white hover:shadow-lg hover:-translate-y-0.5 cursor-pointer">
                  Book Now
                </button>
              </a>
            </div>
          </div>
        </div>
        <div class="relative w-2/3 md:w-1/2 lg:w-1/3 overflow-hidden rounded-xs">
          <img src="/assets/images/suite.jpg" alt="suite room" class="w-full h-full object-cover">
          <div class="absolute bottom-0 p-2 w-full text-white text-xl font-bold
                  bg-[linear-gradient(90deg,rgba(252,237,181,0.5)_0%,rgba(113,70,35,0.5)_100%)]">
            <div class="flex flex-col gap-1">
              <div class="flex justify-between">

                <!-- TODO: Add Suite -->
                <h1 class="text-stone-800 text-md font-crimson font-normal">Suite</h1>
                <p class="w-56 text-yellow-100 text-xl font-crimson font-normal">from ₱ 3,000.00 / per night</p>
              </div>
              <p class="text-white text-sm font-light font-roboto [text-shadow:0px_1px_1px_rgb(0_0_0/0.25)]">
                Experience luxury and spacious living in our suites, offering elegant design, premium amenities, and
                the
                perfect space to unwind.
              </p>
              <a href="/search?room_type=suite&auto=1">
                <button
                  class="self-start text-stone-800 border border-stone-800 px-2 py-1 rounded font-normal font-roboto transition duration-300 ease-in-out transform hover:bg-stone-800 hover:text-white hover:shadow-lg hover:-translate-y-0.5 cursor-pointer">
                  Book Now
                </button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Feedback Section -->
  <div class="grid grid-cols-5 gap-4 p-8 scroll-mt-25" id="about-contact">
    <!-- LEFT rectangle (spans 3 columns) -->
    <div class="col-span-3 space-y-4 p-10 relative">
      <div class="flex flex-col justify-between gap-2">
        <h1 class="text-5xl font-normal font-crimson text-yellow-900 text-center">
          What Our Guests Say?
        </h1>
        <p class="text-xl font-light font-roboto text-neutral-700 text-center mx-20">
          Here are some valuable feedbacks from our guests.
        </p>
        <div class="h-1 w-48 mx-auto bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
      </div>
      <div class="grid grid-cols-[auto_1fr] gap-6 items-center">
        <!-- Quotation Mark -->
        <div class="text-black/10 text-[400px] font-normal font-['Playfair_Display'] text-center">
          “
        </div>

        <!-- Text and autor -->
        <div class="flex flex-col justify-center">
          <p id="testimonial-text" class="text-neutral-800 mb-4 transition-opacity duration-300 opacity-100">
            The hotel exceeded my expectations in so many ways. The deluxe room was spacious and stylish, and all the
            amenities were top-notch. I spent most of my afternoons at the outdoor swimming pool, which was clean and
            very well maintained. The gym was fully equipped, which was a nice bonus since I try to keep up with my
            workouts while traveling.
          </p>
          <div class="flex items-center space-x-2 justify-end">
            <h6 class="font-semibold" id="testimonial-author">Shan L. —</h6>
            <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
            <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
            <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
            <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
            <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
          </div>
        </div>
      </div>

      <!-- Vertical Divider (inside left container, spans full height) -->
      <div class="absolute top-0 right-0 h-full w-px bg-linear-to-b from-yellow-100 to-yellow-900"></div>
    </div>

    <!-- Right Side: Logo + Booking & Support -->
    <div class="col-span-2 flex flex-col items-center space-y-8">
      <!-- Logo -->
      <img src="/assets/images/logo.jpg" alt="logo" class="w-64 -mt-16">

      <!-- Horizontal divider between logo and info -->
      <div class="h-px w-full bg-linear-to-r from-yellow-100 to-yellow-900"></div>

      <!-- Booking & Support Section -->
      <div class="w-full space-y-6 pl-20 pr-20">
        <!-- Booking & Support -->
        <div class="space-y-2">
          <h4 class="text-2xl font-normal font-['Crimson_Text'] text-stone-800">
            Booking & Support
          </h4>
          <a href="/bookings" class="text-xl font-light font-['Roboto'] text-yellow-900 hover:underline block">
            &gt; Manage Booking
          </a>

          <a href="/terms" class="text-xl font-light font-['Roboto'] text-yellow-900 hover:underline block">
            &gt; Terms & Conditions
          </a>
        </div>

        <!-- Address -->
        <div class="space-y-2">
          <h4 class="text-2xl font-normal font-['Crimson_Text'] text-stone-800">
            Address
          </h4>
          <p class="text-xl font-light font-['Roboto'] text-yellow-900">
            Located at Lot 9 Block B Subic Commercial and Light Park, CBD Area, Subic Bay Freeport Zone, Olongapo,
            Philippines, 2222
          </p>
        </div>

        <!-- Contact -->
        <div class="space-y-2">
          <h4 class="text-2xl font-normal font-['Crimson_Text'] text-stone-800">
            Contact
          </h4>
          <p class="text-xl font-light font-['Roboto'] text-yellow-900">
            subicriviera@gmail.com
          </p>
          <p class="text-xl font-light font-['Roboto'] text-yellow-900">
            (01) 123 45 678
          </p>
        </div>

        <div class="flex items-center space-x-2 ">
          <a href="#">
            <img src="/assets/icons/facebook.svg" alt="facebook icon">
          </a>
          <a href="#">
            <img src="/assets/icons/instagram.svg" alt="instagram icon">
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/components/footer.view.php'; ?>

  <script src="/js/testimonials.js"></script>
  <script src="/js/gallery.js"></script>
  <script src="/js/daterange.js"></script>
  <script>
    const openBtn = document.getElementById("openRooms");
    const closeBtn = document.getElementById("closeRooms");
    const modal = document.getElementById("RoomsPopup");

    openBtn.addEventListener("click", () => {
      modal.classList.remove("hidden"); // show modal
    });

    closeBtn.addEventListener("click", () => {
      modal.classList.add("hidden"); // hide modal
    });

    // Optional: close when clicking outside the modal
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.classList.add("hidden");
      }
    });
  </script>
</body>

</html>