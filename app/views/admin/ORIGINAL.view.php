<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lance Hotel Admin</title>
  <link href="/css/output.css" rel="stylesheet">
  <link href="/css/admin/admin.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-stone-200 font-roboto flex flex-col min-h-screen">
  <!-- Sidebar -->
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>

  <!-- Top Bar -->
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <!-- Main Content -->
  <main class="ml-64 mt-15.5 p-4">

    <div class="flex flex-col gap-4 justify-between">
      <?php include_once __DIR__ . '/components/dashboard/Bookings.view.php'; ?>

      <?php include_once __DIR__ . '/components/dashboard/RoomAvailability.view.php'; ?>

      <?php include_once __DIR__ . '/components/dashboard/Reservations.view.php'; ?>

      <div class="flex justify-between gap-4">
        <?php include_once __DIR__ . '/components/dashboard/BookingbyPlatform.view.php'; ?>

        <?php include_once __DIR__ . '/components/dashboard/TopPerforming.view.php'; ?>
      </div>

    </div>

    <!-- Other pages placeholders -->
    <div id="reservations" class="page">
      <h2 class="text-xl font-bold">Reservations</h2>

      <!-- Container 2: Search & Filter -->
      <div class="relative transform translate-x-97 -translate-y-35  ">
        <div class="flex justify-center mt-[200px]">
          <div class="bg-white p-6 border-2 rounded-3xl  shadow mb-8 w-[750px] min-h-[450px]">
            <h3 class="text-lg font-bold mb-4">Search & Filter Reservations</h3>
            <div class="flex space-x-4">
              <input type="text" id="searchGuest" placeholder="Guest Name" class="border p-2 rounded w-1/4">
              <input type="date" id="searchDate" class="border p-2 rounded flex-1">
              <select id="searchStatus" class="border p-2 rounded flex-1">
                <option value="">All</option>
                <option>Pending</option>
                <option>Confirmed</option>
                <option>Cancelled</option>
              </select>
              <button onclick="applyFilter()" class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer">Apply Filter</button>
              <button onclick="resetFilter()" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">Reset Filter</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Container 3: Live Reservation Feed -->
      <div class="relative transform -translate-x-177 -translate-y-52 ">
        <div class="flex justify-center mt-[-200px]">
          <div class="absolute -top-54 left-180 bg-white p-6 border-2 rounded-3xl  shadow w-[750px] min-h-[450px]">
            <h3 class="text-lg font-bold mb-4">Live Reservation Feed</h3>
            <div id="liveFeed" class="flex-1 space-y-2 h-full overflow-y-auto"></div>
          </div>
        </div>
      </div>

      <!-- Container 4: Reservation List -->
      <div class="relative">
        <div class="absolute top-13 left-10 bg-white p-6 border-2 rounded-3xl  shadow mb-80 w-[1480px]">
          <h3 class="text-lg font-bold mb-4">Reservation List</h3>
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-200">
                <th class="border p-2 text-center">ID</th>
                <th class="border p-2 text-center">Booking Code</th>
                <th class="border p-2 text-center">Guest Name</th>
                <th class="border p-2 text-center">Check-in</th>
                <th class="border p-2 text-center">Check-out</th>
                <th class="border p-2 text-center">Method</th>
                <th class="border p-2 text-center">Status</th>
                <th class="border p-2 text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="reservationTable"></tbody>
          </table>
        </div>
      </div>

      <!-- Edit Modal -->
      <div id="editModal" class="fixed inset-0 hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white p-6 rounded rounded-3xl border-2 shadow-lg w-[800px]"> <!-- increased width -->
          <h2 class="text-lg font-bold mb-4">Edit Reservation</h2>
          <form id="editForm" class="grid grid-cols-2 gap-4"> <!-- two-column grid -->
            <div>
              <label class="block text-sm font-medium mb-1">Guest Name</label>
              <input type="text" class="border p-2 w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Room</label>
              <input type="number" class="border p-2 w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Room Type</label>
              <input type="text" class="border p-2 w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Guests</label>
              <input type="number" class="border p-2 w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Check-in</label>
              <input type="date" class="border p-2 w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Check-out</label>
              <input type="date" class="border p-2 w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Method</label>
              <select class="border p-2 w-full">
                <option>Online</option>
                <option>Walk-in</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Status</label>
              <select class="border p-2 w-full">
                <option>Pending</option>
                <option>Confirmed</option>
                <option>Cancelled</option>
              </select>
            </div>
          </form>
          <div class="flex justify-end space-x-2 mt-6">
            <button id="confirmEditBtn" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer">Confirm</button>
            <button id="cancelEditBtn" class="bg-gray-500 text-white px-4 py-2 rounded cursor-pointer">Cancel</button>
          </div>
        </div>
      </div>






      <!-- Container 5: Reservation Details (Modal) -->
      <div id="detailsModal" class="modal">
        <div class="modal-content">
          <h3 class="text-lg font-bold mb-4 text-center">Reservation Details</h3>
          <div id="detailsContent"></div>
          <div class="mt-4 flex justify-center space-x-2">
            <button onclick="closeModal()" class="bg-red-500 text-white px-4 py-2 rounded">Close</button>
          </div>
        </div>
      </div>

      <!-- Cancel Confirmation Modal -->
      <div id="cancelModal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center w-[300px]">
          <h3 class="text-lg font-bold mb-4">Cancel Reservation?</h3>
          <p class="mb-6">Are you sure you want to cancel this reservation?</p>
          <div class="flex justify-center space-x-4">
            <button id="confirmCancelBtn" class="bg-red-500 text-white px-4 py-2 rounded">Yes, Cancel</button>
            <button id="closeCancelBtn" class="bg-gray-500 text-white px-4 py-2 rounded">No</button>
          </div>
        </div>
      </div>





    </div>



    <div id="rooms" class="page">
      <h2 class="text-xl font-bold">Rooms</h2>

      <!-- Add Room Button -->
      <div class="flex flex-col items-center mt-6">
        <!-- Title -->
        <h2 class="relative inline-block text-5xl font-extrabold 
           bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent 
           mb-6 mt-15 drop-shadow-lg flex items-center justify-center gap-2">
          <!-- Icon -->
          <ion-icon name="business-outline" class="text-gray-900 text-4xl"></ion-icon>
          Room Management
          <!-- Gradient underline accent -->
          <span class="absolute left-0 -bottom-2 w-full h-1 
               bg-gradient-to-r from-gray-900 to-gray-600 rounded"></span>
        </h2>


        <!-- Button (content-sized) -->
        <button id="add-room-btn"
          class="bg-gradient-to-r from-green-500 to-green-500 text-white px-6 py-2 border-1 rounded-3xl  font-semibold hover:shadow-md transition mt-5">
          ADD ROOM
        </button>
      </div>

      <!-- Overlay -->
      <div id="room-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-[1px] hidden z-40 border-1 rounded-3xl ">
      </div>

      <!-- Add Room Popup -->
      <div id="add-room-popup" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
              w-[800px] bg-white border-2 rounded-3xl  shadow-2xl p-6 hidden 
              transition-transform duration-300 scale-0 z-50">

        <!-- Close Button -->
        <button id="close-room-popup" class="absolute top-5 right-7 w-8 h-8 flex items-center justify-center 
                  bg-gray-800 text-white rounded-full hover:bg-gray-600 transition">
          ✕
        </button>

        <!-- Create Room Form -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6 ">Create Room</h2>
        <form id="create-room-form" class="flex flex-col gap-4">

          <!-- Room Type -->
          <div>
            <label class="block text-gray-700 font-medium mb-2 flex items-center gap-2">
              <ion-icon name="diamond"></ion-icon> Room Type
            </label>

            <div class="flex items-center gap-3">
              <!-- Narrow input -->
              <input id="room-type" type="text" placeholder="Enter room type"
                class="w-[600px] border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

              <!-- Occupancy buttons -->
              <div class="flex gap-2">
                <button type="button"
                  class="occupancy-btn bg-gray-200 text-gray-800 px-3 py-2 rounded-3xl border-1 hover:bg-gray-300 transition"
                  data-occupancy="single">Single</button>
                <button type="button"
                  class="occupancy-btn bg-gray-200 text-gray-800 px-3 py-2 rounded-3xl border-1 hover:bg-gray-300 transition"
                  data-occupancy="double">Double</button>
              </div>
            </div>

            <!-- Hidden field to store occupancy -->
            <input type="hidden" id="room-occupancy" name="room_occupancy" value="single">
          </div>

          <!-- Room Price -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">
              <ion-icon name="pricetag"></ion-icon> Room Price
            </label>

            <div class="relative">
              <!-- Peso sign -->
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-600 font-bold">₱</span>

              <!-- Input field -->
              <input id="room-price" type="text" placeholder="Enter room price"
                class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
          </div>
          <!-- Room Description -->
          <div>
            <label class="block text-gray-700 font-medium mb-2"><ion-icon name="bookmark"></ion-icon> Room
              Description</label>
            <textarea id="room-desc" placeholder="Enter room description"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
          </div>
          <!-- Room Picture -->
          <div>
            <label class="block text-gray-700 font-medium mb-2"><ion-icon name="image"></ion-icon> Room
              Picture</label>
            <input id="room-pic" type="file" accept="image/*"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          </div>
          <!-- Submit -->
          <button type="submit"
            class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-md transition">
            Create Room
          </button>
        </form>
      </div>

      <!-- Existing Rooms -->
      <div id="room-container"
        class="grid grid-cols-3 justify-items-center gap-x-4 gap-y-6 mt-10 px-6 mx-auto max-w-[1100px]">
        <!-- Standard Room -->
        <div
          class="bg-white border-1 rounded-3xl shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">
          <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
            <img src="/assets/images/standard.jpg" alt="Standard Room" class="w-full h-full object-cover rounded-lg" />
          </div>
          <div class="text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Standard Room (Single)</h3>
            <h2 class="text-lg font-bold text-green-600 mb-2">₱ 1,800</h2>
            <p class="text-gray-600 mb-4">Affordable comfort with essential amenities.</p>
          </div>
          <div class="flex gap-3">
            <button
              class="edit-btn bg-blue-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-blue-600 transition">Edit</button>
            <button
              class="delete-btn bg-red-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-red-600 transition">Delete</button>
          </div>
        </div>


        <!-- Deluxe Room -->
        <div
          class="bg-white border-1 rounded-3xl  shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">
          <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
            <img src="/assets/images/deluxe.jpg" alt="Deluxe Room" class="w-full h-full object-cover rounded-lg" />
          </div>
          <div class="text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Deluxe Room (Single)</h3>
            <h2 class="text-lg font-bold text-green-600  mb-2">₱ 2,300</h2>
            <p class="text-gray-600 mb-4">Spacious room that has all the premium features.</p>
          </div>
          <div class="flex gap-3">
            <button
              class="edit-btn bg-blue-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-blue-600 transition">Edit</button>
            <button
              class="delete-btn bg-red-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-red-600 transition">Delete</button>
          </div>
        </div>

        <!-- Suite Room -->
        <div
          class="bg-white border-1 rounded-3xl shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">
          <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
            <img src="/assets/images/suite.jpg" alt="Suite Room" class="w-full h-full object-cover rounded-lg" />
          </div>
          <div class="text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Suite Room (Single)</h3>
            <h2 class="text-lg font-bold text-green-600  mb-2">₱ 3,000</h2>
            <p class="text-gray-600 mb-4">Luxury suite with the ultimate top-class amenities.</p>
          </div>
          <div class="flex gap-3">
            <button
              class="edit-btn bg-blue-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-blue-600 transition">Edit</button>
            <button
              class="delete-btn bg-red-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-red-600 transition">Delete</button>
          </div>
        </div>

        <!-- Standard Room -->
        <div
          class="bg-white border-1 rounded-3xl shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">
          <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
            <img src="/assets/images/standard.jpg" alt="Standard Room" class="w-full h-full object-cover rounded-lg" />
          </div>
          <div class="text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Standard Room (Double)</h3>
            <h2 class="text-lg font-bold text-green-600 mb-2">₱ 2,700</h2>
            <p class="text-gray-600 mb-4">Affordable comfort with essential amenities.</p>
          </div>
          <div class="flex gap-3">
            <button
              class="edit-btn bg-blue-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-blue-600 transition">Edit</button>
            <button
              class="delete-btn bg-red-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-red-600 transition">Delete</button>
          </div>
        </div>
        <!-- Deluxe Room -->
        <div
          class="bg-white border-1 rounded-3xl  shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">
          <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
            <img src="/assets/images/deluxe.jpg" alt="Deluxe Room" class="w-full h-full object-cover rounded-lg" />
          </div>
          <div class="text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Deluxe Room (Double)</h3>
            <h2 class="text-lg font-bold text-green-600  mb-2">₱ 3,200</h2>
            <p class="text-gray-600 mb-4">Spacious room that has all the premium features.</p>
          </div>
          <div class="flex gap-3">
            <button
              class="edit-btn bg-blue-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-blue-600 transition">Edit</button>
            <button
              class="delete-btn bg-red-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-red-600 transition">Delete</button>
          </div>
        </div>
        <!-- Suite Room -->
        <div
          class="bg-white border-1 rounded-3xl  shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">
          <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
            <img src="/assets/images/suite.jpg" alt="Deluxe Room" class="w-full h-full object-cover rounded-lg" />
          </div>
          <div class="text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Suite Room (Double)</h3>
            <h2 class="text-lg font-bold text-green-600  mb-2">₱ 4,000</h2>
            <p class="text-gray-600 mb-4">Luxury suite with the ultimate top-class amenities.</p>
          </div>
          <div class="flex gap-3">
            <button
              class="edit-btn bg-blue-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-blue-600 transition">Edit</button>
            <button
              class="delete-btn bg-red-500 text-white px-4 py-2 border-1 rounded-3xl hover:bg-red-600 transition">Delete</button>
          </div>
        </div>

      </div>


      <!-- Delete Confirmation Popup -->
      <div id="delete-popup"
        class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-[400px] text-center border-2 border-gray-900">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Confirm Delete</h2>
          <p class="text-gray-600 mb-6">Are you sure you want to delete this room?</p>
          <div class="flex gap-3">
            <button id="cancel-delete" class="flex-1 bg-gray-400 text-white py-2 rounded hover:bg-gray-500 transition">
              Cancel
            </button>
            <button id="confirm-delete" class="flex-1 bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
              Delete
            </button>
          </div>
        </div>
      </div>

      <script>
        let editingRoom = null;   // Track which room is being edited
        let deletingRoom = null;  // Track which room is being deleted

        // Price input formatting
        const priceInput = document.getElementById("room-price");
        priceInput.addEventListener("input", function () {
          this.value = this.value.replace(/[^0-9.]/g, ""); // allow only digits + decimal
        });
        priceInput.addEventListener("input", function (e) {
          let value = this.value.replace(/[^0-9.]/g, "");
          if (value) {
            let parts = value.split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            if (parts[1]) {
              parts[1] = parts[1].substring(0, 2);
            }
            this.value = parts.join(".");
          }
        });

        // Occupancy button logic
        function setupOccupancyButtons() {
          const occupancyField = document.getElementById("room-occupancy");
          document.querySelectorAll(".occupancy-btn").forEach(btn => {
            btn.addEventListener("click", function () {
              const occupancy = this.getAttribute("data-occupancy");
              occupancyField.value = occupancy;

              // Reset buttons in same group
              const container = this.closest(".flex");
              container.querySelectorAll(".occupancy-btn").forEach(b => {
                b.classList.remove("bg-green-500", "text-white");
                b.classList.add("bg-gray-200", "text-gray-800");
              });

              // Highlight clicked
              this.classList.remove("bg-gray-200", "text-gray-800");
              this.classList.add("bg-green-500", "text-white");
            });
          });
        }
        setupOccupancyButtons();

        // Open popup for Add Room
        document.getElementById("add-room-btn").addEventListener("click", () => {
          document.getElementById("room-overlay").classList.remove("hidden");
          document.getElementById("add-room-popup").classList.remove("hidden");
          document.getElementById("add-room-popup").classList.remove("scale-0");
          editingRoom = null;
          document.querySelector("#create-room-form h2").textContent = "Create Room";
        });

        // Close popup
        document.getElementById("close-room-popup").addEventListener("click", () => {
          document.getElementById("room-overlay").classList.add("hidden");
          document.getElementById("add-room-popup").classList.add("hidden");
          document.getElementById("add-room-popup").classList.add("scale-0");
          editingRoom = null;
        });

        // Close when clicking overlay
        document.getElementById("room-overlay").addEventListener("click", () => {
          document.getElementById("room-overlay").classList.add("hidden");
          document.getElementById("add-room-popup").classList.add("hidden");
          document.getElementById("add-room-popup").classList.add("scale-0");
          editingRoom = null;
        });

        // Handle form submit (Add or Edit)
        document.getElementById("create-room-form").addEventListener("submit", function (e) {
          e.preventDefault();

          const type = document.getElementById("room-type").value;
          const price = document.getElementById("room-price").value;
          const desc = document.getElementById("room-desc").value;
          const occupancy = document.getElementById("room-occupancy").value; // single or double
          const picInput = document.getElementById("room-pic");
          let picURL = "placeholder.jpg";

          if (picInput.files && picInput.files[0]) {
            picURL = URL.createObjectURL(picInput.files[0]);
          }

          if (editingRoom) {
            // Update existing room
            editingRoom.querySelector("h3").textContent = `${type} (${occupancy.charAt(0).toUpperCase() + occupancy.slice(1)})`;
            editingRoom.querySelector("h2").textContent = `₱ ${price}`;
            editingRoom.querySelector("p").textContent = desc;
            editingRoom.querySelector("img").src = picURL;
            editingRoom = null;
          } else {
            // Create new room card
            const newRoom = document.createElement("div");
            newRoom.className = "bg-white border-1 rounded-3xl shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center";
            newRoom.innerHTML = `
      <div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
        <img src="${picURL}" alt="${type}" class="w-full h-full object-cover rounded-lg" />
      </div>
      <div class="text-center">
        <h3 class="text-lg font-bold text-gray-800 mb-2">${type} (${occupancy.charAt(0).toUpperCase() + occupancy.slice(1)})</h3>
        <h2 class="text-lg font-bold text-green-600 mb-2">₱ ${price}</h2>
        <p class="text-gray-600 mb-4">${desc}</p>
      </div>
      <div class="flex gap-3">
        <button class="edit-btn bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Edit</button>
        <button class="delete-btn bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Delete</button>
      </div>
    `;
            document.getElementById("room-container").appendChild(newRoom);
          }

          // Close popup
          document.getElementById("room-overlay").classList.add("hidden");
          document.getElementById("add-room-popup").classList.add("hidden");
          document.getElementById("add-room-popup").classList.add("scale-0");

          // Reset form
          this.reset();
          document.getElementById("room-occupancy").value = "single"; // reset to default
        });

        // Delegate Edit/Delete button clicks
        document.getElementById("room-container").addEventListener("click", function (e) {
          if (e.target.classList.contains("edit-btn")) {
            editingRoom = e.target.closest("div.bg-white");
            const titleText = editingRoom.querySelector("h3").textContent;
            // Split out occupancy if present
            const [roomType, occLabel] = titleText.split(" (");
            document.getElementById("room-type").value = roomType.trim();
            if (occLabel) {
              const occ = occLabel.replace(")", "").toLowerCase();
              document.getElementById("room-occupancy").value = occ;
              // highlight correct button
              document.querySelectorAll(".occupancy-btn").forEach(b => {
                b.classList.remove("bg-green-500", "text-white");
                b.classList.add("bg-gray-200", "text-gray-800");
                if (b.getAttribute("data-occupancy") === occ) {
                  b.classList.remove("bg-gray-200", "text-gray-800");
                  b.classList.add("bg-green-500", "text-white");
                }
              });
            }

            document.getElementById("room-price").value = editingRoom.querySelector("h2").textContent.replace("₱ ", "");
            document.getElementById("room-desc").value = editingRoom.querySelector("p").textContent;

            document.getElementById("room-overlay").classList.remove("hidden");
            document.getElementById("add-room-popup").classList.remove("hidden");
            document.getElementById("add-room-popup").classList.remove("scale-0");

            document.querySelector("#create-room-form h2").textContent = "Edit Room";
            document.querySelector("#create-room-form button[type='submit']").textContent = "Update Room";
          }

          if (e.target.classList.contains("delete-btn")) {
            deletingRoom = e.target.closest("div.bg-white");
            document.getElementById("delete-popup").classList.remove("hidden");
          }
        });

        // Delete confirmation
        document.getElementById("cancel-delete").addEventListener("click", () => {
          deletingRoom = null;
          document.getElementById("delete-popup").classList.add("hidden");
        });
        document.getElementById("confirm-delete").addEventListener("click", () => {
          if (deletingRoom) {
            deletingRoom.remove();
            deletingRoom = null;
          }
          document.getElementById("delete-popup").classList.add("hidden");
        });
      </script>

    </div>



    <div id="payments" class="page">
      <h2 class="text-xl font-bold">Payments</h2>
      <!-- Main Payments Table -->
      <!-- Main Payments Table -->
      <div class="overflow-x-auto mt-15 px-6 w-[820px] -ml-4">
        <!-- Title -->
        <h2 class="relative inline-block text-4xl font-extrabold 
           bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent 
           mb-6 mt-10 ml-68 drop-shadow-lg flex items-center justify-center gap-5">
          <!-- Icon -->
          <ion-icon name="pricetag-outline" class="text-gray-900 text-xl"></ion-icon>
          Payments
          <!-- Gradient underline accent -->
          <span class="absolute left-0 -bottom-2 w-full h-1 
               bg-gradient-to-r from-gray-900 to-gray-600 rounded"></span>
        </h2>
        <table class="min-w-full border border-gray-300 rounded-2xl shadow-lg bg-white overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-3 text-center border-r">Guest Name</th>
              <th class="px-6 py-3 text-center border-r">Room Number</th>
              <th class="px-6 py-3 text-center border-r">Check-in / Check-out</th>
              <th class="px-6 py-3 text-center border-r">Status</th>
              <th class="px-6 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="payment-rows" class="divide-y divide-gray-200">
            <!-- Rows will be added based sa inputs ng user -->
          </tbody>
        </table>
      </div>


      <!-- Refund Table -->
      <div class="absolute top-[112px] left-267 px-6 w-[800px]">

        <!-- Title -->
        <h2 class="relative inline-block text-4xl font-extrabold 
             bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent 
             mb-6 mt-10 drop-shadow-lg flex items-center justify-center gap-5">
          <!-- Icon -->
          <ion-icon name="refresh-circle-outline" class="text-gray-900 text-2xl"></ion-icon>
          Refunded Payments
          <span class="absolute left-0 -bottom-2 w-full h-1 
                 bg-gradient-to-r from-gray-900 to-gray-600 rounded"></span>
        </h2>

        <!-- Table -->
        <table class="min-w-full border border-gray-300 rounded-3xl shadow-lg bg-white overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-6 text-center border-r">Guest Name</th>
              <th class="px-6 py-3 text-center border-r">Room Number</th>
              <th class="px-6 py-3 text-center border-r">Check-in / Check-out</th>
              <th class="px-6 py-3 text-center border-r">Status</th>
              <th class="px-6 py-3 text-center">Action</th>
            </tr>
          </thead>
          <tbody id="refund-rows" class="divide-y divide-gray-200">
            <!-- Refunded rows will appear here -->
          </tbody>
        </table>
      </div>

      <!-- Refund Confirmation Popup -->
      <div id="refund-popup"
        class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl p-6 w-[400px] text-center border-2 border-gray-900">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Confirm Refund</h2>
          <p class="text-gray-600 mb-6">Are you sure you want to refund this payment?</p>
          <div class="flex justify-center gap-4">
            <button id="cancel-refund"
              class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Cancel</button>
            <button id="proceed-refund"
              class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Proceed</button>
          </div>
        </div>
      </div>

      <!-- View Popup -->
      <div id="view-popup"
        class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl p-6 w-[400px] text-center border-2 border-gray-900">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment Details</h2>

          <p id="view-roomtype" class="text-gray-600 mb-2"></p>
          <p id="view-roomprice" class="text-gray-600 mb-2"></p>
          <p id="view-method" class="text-gray-600 mb-2"></p>
          <p id="view-nights" class="text-gray-600 mb-2"></p>
          <p id="view-finalamount" class="text-gray-600 mb-4"></p>
          <button id="close-view"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Close</button>
        </div>
      </div>


      <script>
        const paymentRows = document.getElementById("payment-rows");
        const refundRows = document.getElementById("refund-rows");

        let rowToRefund = null; // temporary storage for the row

        // Example function to add a payment row
        function addPayment(guest, room, dates, status, method, amount, nights) {
          const tr = document.createElement("tr");
          tr.innerHTML = `
    <td class="px-6 py-4 text-center border-r">${guest}</td>
    <td class="px-6 py-4 text-center border-r">${room}</td>
    <td class="px-6 py-4 text-center border-r">${dates}</td>
    <td class="px-6 py-4 text-center border-r">
      <span class="px-3 py-1 rounded-full text-sm font-semibold ${status === "Paid" ? "bg-green-100 text-green-700" :
              status === "Pending" ? "bg-yellow-100 text-yellow-700" :
                "bg-red-100 text-red-700"
            }">${status}</span>
      </td>
      <td class="px-6 py-4 flex justify-center gap-2">
        <button class="view-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition"
          data-method="${method}" data-amount="${amount}" data-nights="${nights}">View</button>
        <button class="refund-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Refund</button>
      </td>
    `;
          paymentRows.appendChild(tr);
        }

        // Handle View button
        paymentRows.addEventListener("click", function (e) {
          if (e.target.classList.contains("view-btn")) {
            document.getElementById("view-roomtype").textContent = "Room Type: " + e.target.dataset.roomtype;
            document.getElementById("view-roomprice").textContent = "Room Price: ₱ " + e.target.dataset.roomprice;
            document.getElementById("view-method").textContent = "Method: " + e.target.dataset.method;
            document.getElementById("view-nights").textContent = "Nights: " + e.target.dataset.nights;
            document.getElementById("view-finalamount").textContent = "Final Amount: ₱ " + e.target.dataset.finalamount;
            document.getElementById("view-popup").classList.remove("hidden");
          }
        });

        document.getElementById("close-view").addEventListener("click", () => {
          document.getElementById("view-popup").classList.add("hidden");
        });

        // Handle Refund button → only show popup
        paymentRows.addEventListener("click", function (e) {
          if (e.target.classList.contains("refund-btn")) {
            rowToRefund = e.target.closest("tr");
            document.getElementById("refund-popup").classList.remove("hidden");
          }
        });

        // Cancel refund
        document.getElementById("cancel-refund").addEventListener("click", () => {
          rowToRefund = null; // reset
          document.getElementById("refund-popup").classList.add("hidden");
        });

        // Proceed refund
        document.getElementById("proceed-refund").addEventListener("click", () => {
          if (rowToRefund) {
            refundRows.appendChild(rowToRefund);
            rowToRefund.querySelector("td:last-child").remove();
            rowToRefund.querySelector("span").textContent = "Cancelled";
            rowToRefund.querySelector("span").className = "px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700";

            // Undo button
            const undoCell = document.createElement("td");
            undoCell.className = "px-6 py-4 text-center";
            undoCell.innerHTML = `<button class="undo-btn bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition">Undo</button>`;
            rowToRefund.appendChild(undoCell);

            rowToRefund = null;
          }
          document.getElementById("refund-popup").classList.add("hidden");
        });

        refundRows.addEventListener("click", function (e) {
          if (e.target.classList.contains("undo-btn")) {
            const row = e.target.closest("tr");
            row.querySelector("td:last-child").remove();
            row.querySelector("span").textContent = "Paid";
            row.querySelector("span").className = "px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700";
            const actionCell = document.createElement("td");
            actionCell.className = "px-6 py-4 flex justify-center gap-2";
            actionCell.innerHTML = `
      <button class="view-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition cursor-pointer">View</button>
      <button class="refund-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition cursor-pointer">Refund</button>
    `;
            row.appendChild(actionCell);
            paymentRows.appendChild(row);
          }
        });

        // Example: Add sample data
        addPayment("Juan Dela Cruz", "101", "Mar 25 – Mar 28", "Paid", "Walk-in", "1800", "3");
        addPayment("Maria Santos", "202", "Apr 1 – Apr 3", "Pending", "Online", "2300", "2");
        addPayment("Jonathan Shun", "101", "Mar 25 – Mar 28", "Paid", "Walk-in", "1800", "3");
        addPayment("Jeremy Carl", "202", "Apr 1 – Apr 3", "Pending", "Online", "2300", "2");
        addPayment("Trisha Dela Bola", "101", "Mar 25 – Mar 28", "Paid", "Walk-in", "1800", "3");
        addPayment("Corndog ni Ate", "202", "Apr 1 – Apr 3", "Pending", "Online", "2300", "2");
      </script>
    </div>

    <div id="calendar" class="page">
      <h2 class="text-xl font-bold">Calendar</h2>
      <div class="flex flex-col gap-6 w-full md:flex-row md:items-start">
        <!-- Calendar -->
        <div
          class="bg-white border-1 rounded-3xl shadow-lg w-[900px] h-[780px] flex flex-col overflow-hidden transition-all duration-300 hover: translate-y-14 translate-x-18 hover:shadow-xl">
          <!-- Header -->
          <div class="relative bg-gradient-to-r from-blue-600 to-indigo-500 text-white p-6 text-center">
            <div class="absolute inset-y-1/2 left-0 right-0 flex justify-between px-4 -translate-y-1/2 z-10">
              <button id="prev-month"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition">
                <ion-icon name="caret-back-circle-outline" class="text-2xl"></ion-icon>
              </button>
              <button id="next-month"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition">
                <ion-icon name="caret-forward-circle-outline" class="text-2xl"></ion-icon>
              </button>
            </div>
            <div id="month-year" class="text-xl font-semibold relative">September 2025</div>
          </div>
          <!-- Weekdays -->
          <div class="grid grid-cols-7 px-4 py-2 bg-blue-100 font-semibold text-blue-600">
            <div class="text-center">Sun</div>
            <div class="text-center">Mon</div>
            <div class="text-center">Tue</div>
            <div class="text-center">Wed</div>
            <div class="text-center">Thu</div>
            <div class="text-center">Fri</div>
            <div class="text-center">Sat</div>
          </div>
          <!-- Days -->
          <div id="days" class="grid grid-cols-7 gap-1 p-4 flex-1"></div>
          <!-- Footer -->
          <div class="flex justify-between items-center border-t border-gray-200 p-4">
            <button id="today-btn"
              class="bg-gradient-to-r from-teal-400 to-green-500 text-white px-4 py-2 rounded-full font-medium hover:-translate-y-0.5 hover:shadow-md transition">
              <i class="fas fa-calendar-day mr-2"></i> Today
            </button>
            <div class="flex items-center gap-2 text-teal-500 text-sm">
              <i class="fas fa-circle text-xs"></i>
              <span>Has events</span>
            </div>
          </div>
        </div>
        <!-- Event Panel -->
        <div id="event-panel"
          class="bg-white border-1 rounded-3xl  shadow-lg p-6 w-full max-w-[500px] translate-y-14 translate-x-18">
          <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-blue-200">
            <div id="event-date" class="text-xl font-semibold text-blue-600">Select a date</div>
            <i class="fas fa-calendar-alt text-teal-500 text-lg"></i>
          </div>
          <div id="event-list" class="max-h-[350px] overflow-y-auto">
            <div class="text-center text-gray-500 italic p-8">Select a date with reservations to view them here</div>
          </div>
        </div>
      </div>

    </div>

    <div id="logs" class="page">
      <h2 class="text-xl font-bold">Activity Logs</h2>


      <!-- Activity Logs Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-top:100px;">

        <!-- Container 1: Recent Activity -->
        <div>
          <h2 class="relative inline-block text-4xl font-extrabold 
               bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent 
               mb-10 ml-10 drop-shadow-lg flex items-center gap-5">
            <ion-icon name="shield-checkmark-outline" class="text-gray-900 text-2xl"></ion-icon>
            Admin Logs
            <span class="absolute left-0 -bottom-2 w-full h-1 
                   bg-gradient-to-r from-gray-900 to-gray-600 rounded"></span>
          </h2>

          <div class="bg-white shadow-md rounded-3xl border-2 p-6">
            <h2 class="relative inline-block text-2xl font-bold 
                 bg-gradient-to-r from-green-700 to-green-400 bg-clip-text text-transparent 
                 mb-6 drop-shadow-md flex items-center gap-3">
              <ion-icon name="time-outline" class="text-green-700 text-xl"></ion-icon>
              Recent Activity
              <span class="absolute left-0 -bottom-1 w-full h-0.5 
                     bg-gradient-to-r from-green-700 to-green-400 rounded"></span>
            </h2>

            <table class="w-full text-sm text-left border-collapse">
              <thead>
                <tr class="border-b">
                  <th class="py-2 px-3">Date</th>
                  <th class="py-2 px-3">Action</th>
                </tr>
              </thead>
              <tbody id="recentActivityTable">
                <tr class="border-b">
                  <td class="py-2 px-3">2026-03-31</td>
                  <td class="py-2 px-3">User Login</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Container 2: Archived Logs -->
        <div>
          <h2 class="relative inline-block text-4xl font-extrabold 
               bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent 
               mb-10 ml-10 drop-shadow-lg flex items-center gap-5">
            <ion-icon name="person-circle-outline" class="text-gray-900 text-2xl"></ion-icon>
            User Logs
            <span class="absolute left-0 -bottom-2 w-full h-1 
                   bg-gradient-to-r from-gray-900 to-gray-600 rounded"></span>
          </h2>

          <div class="bg-white shadow-md rounded-3xl border-2 p-6">
            <h2 class="relative inline-block text-2xl font-bold 
                 bg-gradient-to-r from-green-700 to-green-400 bg-clip-text text-transparent 
                 mb-6 drop-shadow-md flex items-center gap-3">
              <ion-icon name="archive-outline" class="text-green-700 text-xl"></ion-icon>
              Archived Logs
              <span class="absolute left-0 -bottom-1 w-full h-0.5 
                     bg-gradient-to-r from-green-700 to-green-400 rounded"></span>
            </h2>

            <table class="w-full text-sm text-left border-collapse">
              <thead>
                <tr class="border-b">
                  <th class="py-2 px-3">Date</th>
                  <th class="py-2 px-3">Action</th>
                </tr>
              </thead>
              <tbody id="archivedLogsTable">
                <tr class="border-b">
                  <td class="py-2 px-3">2026-02-15</td>
                  <td class="py-2 px-3">Payment Refunded</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </main>
  </div>

  <!-- Page toggle -->
  <script>
    // Toggle between pages
    function show(id) {
      document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
      document.getElementById(id).classList.add('active');
    }

    // Revenue chart (line)
    new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: {
        labels: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Revenue',
          data: [200000, 250000, 315060, 280000, 290000, 300000],
          borderColor: '#10b981', // Tailwind green-500 hayop na yan need iconfigure pa
          backgroundColor: '#10b981',
          fill: false,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: false } }
      }
    });

    // Reservations chart (bar)
    new Chart(document.getElementById('reservationChart'), {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [
          { label: 'Booked', data: [40, 50, 60, 30, 70, 80, 55], backgroundColor: '#10b981' },
          { label: 'Canceled', data: [5, 10, 8, 3, 12, 7, 6], backgroundColor: '#ef4444' } // Tailwind red-500 fckthishsit 
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: { stacked: true },
          y: { stacked: true, beginAtZero: true }
        }
      }
    });

    // Reservations chart (This Month - bar)
    new Chart(document.getElementById('monthChart'), {
      type: 'bar',
      data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
          { label: 'Booked', data: [120, 150, 180, 140], backgroundColor: '#3b82f6' },
          { label: 'Canceled', data: [15, 20, 40, 12], backgroundColor: '#f59e0b' }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    // Booking by platform chart (doughnut)
    new Chart(document.getElementById('platformChart'), {
      type: 'doughnut',
      data: {
        labels: ['Direct', 'Booking.com', 'Agoda', 'Airbnb', 'Hotels.com', 'Others'],
        datasets: [{
          data: [61, 12, 11, 9, 5, 2],
          backgroundColor: [
            '#10b981', // Direct - green
            '#3b82f6', // Booking.com - blue
            '#f59e0b', // Agoda - amber
            '#8b5cf6', // Airbnb - purple
            '#ef4444', // Hotels.com - red
            '#6b7280'  // Others - gray
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: -40,
            bottom: 50,
            left: 0,
            right: 5
          }
        },
        plugins: {
          legend: {
            position: 'right',
            align: 'center',
            labels: {
              font: { size: 15 },
              color: '#374151',
              padding: 50,
              generateLabels: function (chart) {
                const data = chart.data;
                if (data.labels.length && data.datasets.length) {
                  return data.labels.map((label, i) => {
                    const value = data.datasets[0].data[i];
                    const backgroundColor = data.datasets[0].backgroundColor[i];
                    return {
                      text: `${label} (${value})`,
                      fillStyle: backgroundColor,
                      strokeStyle: backgroundColor,
                      lineWidth: 2,
                      hidden: isNaN(value),
                      index: i
                    };
                  });
                }
                return [];
              }
            }
          }
        }
      }
    });

    // Sample data lang 
    const roomTypesData = {
      labels: ["Standard", "Deluxe", "Suite"],
      datasets: [{
        data: [83, 62, 36], // example counts
        backgroundColor: [
          "rgba(59, 130, 246, 0.7)",  // Standard
          "rgba(16, 185, 129, 0.7)",  // Deluxe
          "rgba(245, 158, 11, 0.7)"   // Suite
        ],
        borderColor: [
          "rgba(59, 130, 246, 1)",
          "rgba(16, 185, 129, 1)",
          "rgba(245, 158, 11, 1)"
        ],
        borderWidth: 1
      }]
    };

    const config = {
      type: "bar",
      data: roomTypesData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return `${context.label}: ${context.formattedValue}`;
              }
            }
          }
        },
        scales: {
          x: {
            categoryPercentage: 0.6,
            barPercentage: 0.8
          },
          y: {
            title: {
              display: true,
              text: "No. Of Bookings"
            },
            beginAtZero: true
          }
        }
      }
    };

    const ctx = document.getElementById('roomTypesChart').getContext('2d');
    const roomTypesChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Standard', 'Deluxe', 'Suite'],
        datasets: [{
          label: 'Bookings',
          data: [1200, 1500, 800],
          backgroundColor: [
            'rgba(59, 130, 246, 1)',   // Standard - Blue
            'rgba(16, 185, 129, 1)',   // Deluxe - Green
            'rgba(234, 88, 12, 1)'     // Suite - Orange
          ],
          borderColor: [
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(234, 88, 12, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        layout: {
          padding: {
            top: 10,
            bottom: 20,
            left: 10,
            right: 10
          }
        },
        scales: {
          x: {
            ticks: {
              padding: 5
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>
</body>
</html>