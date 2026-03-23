// Disable the dates where there are bookings
flatpickr("#daterange", {
    mode: "range",
    dateFormat: "d/m/Y",
    allowInput: true,
    minDate: "today",
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