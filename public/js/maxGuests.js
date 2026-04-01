document.addEventListener("DOMContentLoaded", () => {
  const roomRadios = document.querySelectorAll('input[name="room"]');
  const guestsInput = document.getElementById('guests');
  const guestsLabel = document.getElementById('guests-label');
  const guestAddon = document.getElementById("guests-addon");

  const roomType = document.body.dataset.roomType;

  const fullConfig = {
    standard: {
      single: { min: 1, max: 2, add: 1 },
      double: { min: 1, max: 3, add: 2 }
    },
    deluxe: {
      single: { min: 1, max: 4, add: 1 },
      double: { min: 1, max: 6, add: 2 }
    },
    suite: {
      single: { min: 1, max: 6, add: 1 },
      double: { min: 1, max: 10, add: 2 }
    }
  };

  function updateGuestUI() {
    const selected = document.querySelector('input[name="room"]:checked');
    if (!selected) return;

    const occupancy = selected.value;
    const config = fullConfig[roomType]?.[occupancy];
    if (!config) return;

    const { min, max, add } = config;

    guestsInput.min = min;
    guestsInput.max = max;

    guestsLabel.textContent = `Guests (Max ${max})`;
    guestAddon.textContent = `Additional guests (above ${add}) cost 10% of the room rate per night.`;

    if (parseInt(guestsInput.value) > max) guestsInput.value = max;
    if (parseInt(guestsInput.value) < min) guestsInput.value = min;
  }

  roomRadios.forEach(radio => {
    radio.addEventListener("change", updateGuestUI);
  });

  updateGuestUI();
});