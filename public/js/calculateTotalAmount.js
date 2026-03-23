// Default values
let nights = 1;
let guests = 1;
let roomType = 'single';

function getNights(checkIn, checkOut) {
    const d1 = new Date(checkIn);
    const d2 = new Date(checkOut);

    // Normalize both to midnight UTC
    const utc1 = Date.UTC(d1.getFullYear(), d1.getMonth(), d1.getDate());
    const utc2 = Date.UTC(d2.getFullYear(), d2.getMonth(), d2.getDate());

    const diffMs = utc2 - utc1;
    return Math.max(0, diffMs / (1000 * 60 * 60 * 24)); // ensure non-negative
}

// Update the price UI
function updatePrice({ nights, guests }) {
    let total = 0;

    const selectedRoom = document.querySelector('input[name="room"]:checked');
    if (!selectedRoom) return;

    const basePrice = Number(selectedRoom.dataset.basePrice);

    total = basePrice * nights;

    let nightDiscount = 0;
    let guestCharge = 0;

    if (nights > 3) {
        nightDiscount = total * 0.15;
    };

    if (selectedRoom.value === "single") {
        guestCharge = basePrice * 0.10 * (guests - 1) * nights;
    } else if (selectedRoom.value === "double") {
        guestCharge = basePrice * 0.10 * (guests - 2) * nights;
    };

    total = (total - nightDiscount + guestCharge) * 1.12;

    document.getElementById("room-price").textContent =
        `₱${basePrice.toLocaleString()}`;
    document.getElementById("total-price").textContent =
        `₱${total.toLocaleString()}`;
}

// Update price function expects nights, guests
function handleUpdate() {
    const guestInput = document.getElementById("guests");
    guests = Number(guestInput.value) || 1;

    // Only update if nights > 0
    if (nights > 0) {
        updatePrice({ nights, guests });
    }
}

// Room radio buttons
const radios = document.querySelectorAll('input[name="room"]');
radios.forEach(radio => {
    radio.addEventListener('change', () => {
        roomType = radio.value;
        handleUpdate();
    });
});

// Guest input
document.getElementById('guests').addEventListener('input', handleUpdate);

// Initialize on page load
document.addEventListener("DOMContentLoaded", handleUpdate);