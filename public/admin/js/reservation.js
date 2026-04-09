// reservations.js

// Sample data
let reservations = [
  { id: 1, bookingcode: "HVKGHJC", guest: "Lance Samonte", room: 101, checkin: "2026-03-23", checkout: "2026-03-25", method: "Online", status: "Pending" },
  { id: 2, bookingcode: "HVKGHJC", guest: "Lourence Cortez", room: 102, checkin: "2026-03-24", checkout: "2026-03-26", method: "Walk-in", status: "Confirmed" },
];

let currentId = reservations.length;
let liveFeed = [];
let currentIndex = 0;

// Utility: render status badge
function statusBadge(status) {
  const cls = status.toLowerCase();
  return `<span class="badge badge-${cls}">${status}</span>`;
}

// Render reservation table
function renderTable(data = reservations) {
  const tbody = document.getElementById('reservationTable');
  tbody.innerHTML = '';
  data.forEach(r => {
    tbody.innerHTML += `
      <tr>
        <td class="border p-2 text-center">${r.id}</td>
        <td class="border p-2 text-center">${r.bookingcode}</td>
        <td class="border p-2 text-center">${r.guest}</td>
        <td class="border p-2 text-center">${r.checkin}</td>
        <td class="border p-2 text-center">${r.checkout}</td>
        <td class="border p-2 text-center">${r.method}</td>
        <td class="border p-2 text-center">${statusBadge(r.status)}</td>
        <td class="border p-2">
          <div class="flex justify-center space-x-2">
            <button onclick="viewReservation(${r.id})" class="bg-blue-500 text-white px-2 py-1 rounded cursor-pointer">View</button>
            <button onclick="editReservation(${r.id})" class="bg-yellow-500 text-white px-2 py-1 rounded cursor-pointer">Edit</button>
            <button onclick="cancelReservation(${r.id})" class="bg-red-500 text-white px-2 py-1 rounded cursor-pointer">Cancel</button>
          </div>
        </td>
      </tr>
    `;
  });
}

// View reservation details
function viewReservation(id) {
  const r = reservations.find(x => x.id === id);
  if (!r) return;
  const modal = document.getElementById('detailsModal');
  const content = document.getElementById('detailsContent');
  content.innerHTML = `
    <div class="text-center">
      <p><strong>ID:</strong> ${r.id}</p>
      <p><strong>Booking Code:</strong> ${r.bookingcode}</p>
      <p><strong>Guest Name:</strong> ${r.guest}</p>
      <p><strong>Room:</strong> ${r.room}</p>
      <p><strong>Room Type:</strong> ${r.roomType || ""}</p>
      <p><strong>Check-in:</strong> ${r.checkin}</p>
      <p><strong>Check-out:</strong> ${r.checkout}</p>
      <p><strong>Method:</strong> ${r.method}</p>
      <p><strong>Status:</strong> ${r.status}</p>
    </div>
  `;
  modal.style.display = "flex";
}
function closeModal() {
  document.getElementById('detailsModal').style.display = "none";
}

// Edit reservation (open modal)
function editReservation(id) {
  const r = reservations.find(x => x.id === id);
  if (!r) return;

  const form = document.getElementById('editForm').elements;
  form[0].value = r.guest;              // Guest Name
  form[1].value = r.room;               // Room
  form[2].value = r.roomType || "Single"; // Room Type
  form[3].value = r.guests || 1;        // Guests
  form[4].value = r.checkin;            // Check-in
  form[5].value = r.checkout;           // Check-out
  form[6].value = r.method;             // Method
  form[7].value = r.status;             // Status

  form[0].dataset.editId = id;

  document.getElementById('editModal').classList.remove('hidden');
}

// Confirm edit
document.getElementById('confirmEditBtn').addEventListener('click', () => {
  const form = document.getElementById('editForm').elements;
  const id = parseInt(form[0].dataset.editId);
  const r = reservations.find(x => x.id === id);
  if (r) {
    r.guest = form[0].value;
    r.room = parseInt(form[1].value);
    r.roomType = form[2].value;
    r.guests = parseInt(form[3].value);
    r.checkin = form[4].value;
    r.checkout = form[5].value;
    r.method = form[6].value;
    r.status = form[7].value;
  }
  renderTable();
  document.getElementById('editModal').classList.add('hidden');
});

// Cancel edit
document.getElementById('cancelEditBtn').addEventListener('click', () => {
  document.getElementById('editModal').classList.add('hidden');
});


// Confirm reservation
function confirmReservation(id) {
  const r = reservations.find(x => x.id === id);
  if (r) r.status = "Confirmed";
  renderTable();
}

let cancelTargetId = null;

function cancelReservation(id) {
  cancelTargetId = id;
  document.getElementById('cancelModal').classList.remove('hidden');
}

document.getElementById('confirmCancelBtn').addEventListener('click', () => {
  const index = reservations.findIndex(x => x.id === cancelTargetId);
  if (index !== -1) {
    reservations.splice(index, 1); // remove the reservation from the array
  }
  renderTable(); // re-render table without the removed reservation
  document.getElementById('cancelModal').classList.add('hidden');
});

document.getElementById('closeCancelBtn').addEventListener('click', () => {
  document.getElementById('cancelModal').classList.add('hidden');
});

// Filtering
function applyFilter() {
  const guest = document.getElementById('searchGuest').value.toLowerCase();
  const date = document.getElementById('searchDate').value;
  const status = document.getElementById('searchStatus').value;
  const filtered = reservations.filter(r =>
    (!guest || r.guest.toLowerCase().includes(guest)) &&
    (!date || r.checkin === date) &&
    (!status || r.status === status)
  );
  renderTable(filtered);
}
function resetFilter() {
  document.getElementById('searchGuest').value = "";
  document.getElementById('searchDate').value = "";
  document.getElementById('searchStatus').value = "";
  renderTable();
}

// Live feed simulation
function addLiveReservation() {
  const names = ["Lance Samonte", "Lourence Cortez", "Joseph Aniag", "Shanna Carreon", "Trisha Torres", "Myra Meijas", "Justine Garcia", "Shun Soco", "Ma'am Betong", "Ma'am Quicay"];
  const methods = ["Online", "Walk-in"];
  const statuses = ["Pending", "Confirmed", "Cancelled"];
  currentId++;
  const newRes = {
    id: currentId,
    guest: names[Math.floor(Math.random() * names.length)],
    room: 100 + Math.floor(Math.random() * 50),
    checkin: "2026-03-" + String(Math.floor(Math.random() * 28) + 1).padStart(2, '0'),
    checkout: "2026-03-" + String(Math.floor(Math.random() * 28) + 1).padStart(2, '0'),
    method: methods[Math.floor(Math.random() * methods.length)],
    status: statuses[Math.floor(Math.random() * statuses.length)]
  };
  liveFeed.push(newRes); // push instead of unshift
  renderLiveFeed();
}

function renderLiveFeed() {
  const container = document.getElementById('liveFeed');
  container.innerHTML = '';
  liveFeed.forEach(r => {
    container.innerHTML += `
      <div class="flex justify-between items-center border border-gray-300 shadow-xs p-2 rounded bg-gray-50">
        <div>
          <p class="font-semibold">${r.guest}</p>
          <p class="text-sm text-gray-600">Room ${r.room} - ${r.method} - ${r.checkin}</p>
        </div>
        <div class="space-x-2">
          <button onclick="viewLiveReservation(${r.id})" class="bg-blue-500 text-white px-2 py-1 rounded cursor-pointer">View</button>
          <button onclick="confirmLiveReservation(${r.id})" class="bg-green-500 text-white px-2 py-1 rounded cursor-pointer">Confirm</button>
          <button onclick="cancelLiveReservation(${r.id})" class="bg-red-500 text-white px-2 py-1 rounded cursor-pointer">Cancel</button>
        </div>
      </div>
    `;
  });

  // Auto-scroll to bottom
  container.scrollTop = container.scrollHeight;
}


// View live reservation (same as viewReservation)
function viewLiveReservation(id) {
  const r = liveFeed.find(x => x.id === id);
  if (!r) return;
  const modal = document.getElementById('detailsModal');
  const content = document.getElementById('detailsContent');
  content.innerHTML = `
    <div class="text-center">
      <p><strong>ID:</strong> ${r.id}</p>
      <p><strong>Booking Code:</strong> ${r.bookingcode || "N/A"}</p>
      <p><strong>Guest Name:</strong> ${r.guest}</p>
      <p><strong>Room:</strong> ${r.room}</p>
      <p><strong>Room Type:</strong> ${r.roomType || ""}</p>
      <p><strong>Check-in:</strong> ${r.checkin}</p>
      <p><strong>Check-out:</strong> ${r.checkout}</p>
      <p><strong>Method:</strong> ${r.method}</p>
      <p><strong>Status:</strong> ${r.status}</p>
    </div>
  `;
  modal.style.display = "flex";
}

// Confirm live reservation → move to reservations list
function confirmLiveReservation(id) {
  const index = liveFeed.findIndex(x => x.id === id);
  if (index !== -1) {
    const r = liveFeed[index];
    r.status = "Confirmed";
    reservations.push(r);   // add to main reservations list
    liveFeed.splice(index, 1); // remove from live feed
    renderTable();
    renderLiveFeed();
  }
}

// Cancel live reservation → remove from live feed
function cancelLiveReservation(id) {
  const index = liveFeed.findIndex(x => x.id === id);
  if (index !== -1) {
    liveFeed.splice(index, 1); // remove from live feed
    renderLiveFeed();
  }
}


// Initialize
renderTable();
setInterval(addLiveReservation, 2000);
