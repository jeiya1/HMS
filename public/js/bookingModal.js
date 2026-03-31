document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action="/cart-submit"]');
    const modal = document.getElementById('booking-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const continueBtn = document.getElementById('continue-browsing');
    const proceedBtn = document.getElementById('proceed-checkout');

    // Convert dd/mm/yyyy to yyyy-mm-dd
    function parseDMY(dateStr) {
        if (!dateStr) return null;
        const parts = dateStr.split('/');
        if (parts.length !== 3) return null;
        const [day, month, year] = parts;
        return `${year}-${month}-${day}`;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        try {
            const roomTypeEl = document.querySelector('input[name="room"]:checked');
            if (!roomTypeEl) throw "No room selected";
            const roomType = roomTypeEl.value;

            const guests = Number(document.getElementById('guests').value);
            const checkinCheckout = document.getElementById('daterange').value;
            const firstImage = document.querySelector('.main-image img').src;

            const dates = checkinCheckout.split(' to ');
            const checkin = dates[0] || '-';
            const checkout = dates[1] || '-';
            const checkinDate = parseDMY(checkin);
            const checkoutDate = parseDMY(checkout);
            const nights = (checkinDate && checkoutDate) ? getNights(checkinDate, checkoutDate) : 1;

            // Prepare form data
            const formData = new FormData(form);

            // AJAX POST to server
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    if (!response.success) {
                        showToast(response.error);
                        return;
                    }

                    // Update left side modal
                    document.getElementById('modal-image').src = firstImage;
                    document.getElementById('modal-room-type').textContent = roomType.charAt(0).toUpperCase() + roomType.slice(1);
                    document.getElementById('modal-guests').textContent = guests + (guests > 1 ? ' Guests' : ' Guest');
                    document.getElementById('modal-checkin-checkout').textContent = checkinCheckout;
                    document.getElementById('modal-room').textContent = roomType.charAt(0).toUpperCase() + roomType.slice(1);

                    // Update right side modal pricing
                    const pricing = calculateTotal({ roomType, nights, guests });

                    if (pricing) {
                        document.getElementById('modal-room-cost').textContent = `₱${pricing.roomCost.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                        document.getElementById('modal-night-discount').textContent = `₱${pricing.nightDiscount.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                        document.getElementById('modal-guest-charge').textContent = `₱${pricing.guestCharge.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                        document.getElementById('modal-total').textContent = `₱${pricing.total.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                    }

                    // Update cart count dynamically
                    if ($('#cart-count').length) {
                        $('#cart-count').text(response.cartCount);
                    } else {
                        $('<span id="cart-count" class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/3 bg-[#FCEDB5] text-black font-crimson text-xs font-bold w-4 h-4 flex items-center justify-center border border-[#FCEDB5]">' + response.cartCount + '</span>').appendTo('a[href="/cart"]');
                    }

                    // Show modal
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(err => {
                    console.error('AJAX request failed:', err);
                    showToast('Something went wrong. Please try again.');
                });

        } catch (err) {
            console.error('Booking modal error:', err);
        }
    });

    // Close modal
    closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
    continueBtn.addEventListener('click', () => modal.classList.add('hidden'));

    // Proceed to cart page
    proceedBtn.addEventListener('click', () => {
        window.location.href = '/cart';
    });
});