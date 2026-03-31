// Get initial values from URL parameters
const urlParams = new URLSearchParams(window.location.search);
const checkin = urlParams.get("checkin");   // e.g., "2026-04-28"
const checkout = urlParams.get("checkout"); // e.g., "2026-05-01"

// Convert Y-m-d string to Date object
function parseYMD(dateStr) {
    if (!dateStr) return null;
    const [year, month, day] = dateStr.split("-").map(Number);
    return new Date(year, month - 1, day); // month is 0-based
}

const defaultDates = [];
if (checkin) defaultDates.push(parseYMD(checkin));
if (checkout) defaultDates.push(parseYMD(checkout));

flatpickr("#daterange", {
    mode: "range",
    dateFormat: "d/m/Y",
    allowInput: true,
    minDate: "today",
    defaultDate: defaultDates.length ? defaultDates : null, // Correctly set Date objects
    onChange: function (selectedDates) {
        if (selectedDates.length === 2) {
            const [checkIn, checkOut] = selectedDates;
            nights = getNights(checkIn, checkOut);
            handleUpdate();
        }
    },
    onClose: function (selectedDates, dateStr, instance) {
        if (!dateStr) {
            instance.set("minDate", "today");
        }
    }
});