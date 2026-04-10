$(document).ready(function() {
    function loadReservations() {
        $.ajax({
            url: '/admin/getConfirmedReservations', // JSON endpoint
            type: 'GET',
            dataType: 'json',
            success: function(reservations) {
                var $table = $('#reservationTable');
                $table.empty();

                if (!reservations || reservations.length === 0) {
                    $table.append('<tr><td colspan="9" class="text-center p-2">No confirmed reservations found.</td></tr>');
                    return;
                }

                reservations.forEach(function(reservation) {
                    var rooms = reservation.Rooms || [];
                    var earliestCheckIn = rooms.map(r => r.CheckInDate).sort()[0] || '';
                    var latestCheckOut = rooms.map(r => r.CheckOutDate).sort().reverse()[0] || '';
                    var totalRoomAmount = rooms.reduce((sum, r) => sum + parseFloat(r.BasePrice || 0), 0).toFixed(2);

                    $table.append(`
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">${reservation.BookingToken}</td>
                            <td class="border border-gray-300 p-2 text-center">${reservation.GuestFirstName} ${reservation.GuestLastName}</td>
                            <td class="border border-gray-300 p-2 text-center">${earliestCheckIn}</td>
                            <td class="border border-gray-300 p-2 text-center">${latestCheckOut}</td>
                            <td class="border border-gray-300 p-2 text-center">${rooms.length}</td>
                            <td class="border border-gray-300 p-2 text-center">$${totalRoomAmount}</td>
                            <td class="border border-gray-300 p-2 text-center">$${reservation.PaymentAmount || '0.00'} (${reservation.PaymentMethod || 'N/A'})</td>
                            <td class="border border-gray-300 p-2 text-center">${reservation.PaymentStatus || 'N/A'}</td>
                            <td class="border border-gray-300 p-2 text-center">
                                <button class="viewReservation cursor-pointer" data-token="${reservation.BookingToken}">View</button>
                                <button class="cancelReservation pointer" data-token="${reservation.BookingToken}">Cancel</button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                $('#reservationTable').html('<tr><td colspan="9" class="text-center text-red-500">Error loading reservations.</td></tr>');
            }
        });
    }

    loadReservations();
});
$(document).on('click', '.viewReservation', function() {
    var token = $(this).data('token');

    // Find the reservation in the loaded table data
    $.ajax({
        url: '/admin/getConfirmedReservations',
        type: 'GET',
        dataType: 'json',
        success: function(reservations) {
            var res = reservations.find(r => r.BookingToken === token);
            if (!res) return alert('Reservation not found.');

            var roomsHtml = '';
            res.Rooms.forEach(function(room) {
                roomsHtml += `
                    <div class="border p-2 rounded mb-2">
                        <p><strong>Room:</strong> ${room.RoomNumber} (${room.RoomType})</p>
                        <p><strong>Guests:</strong> Adults ${room.NumAdults}, Children ${room.NumChildren}</p>
                        <p><strong>Check-in:</strong> ${room.CheckInDate}</p>
                        <p><strong>Check-out:</strong> ${room.CheckOutDate}</p>
                        <p><strong>Base Price:</strong> $${room.BasePrice}</p>
                    </div>
                `;
            });

            $('#detailsContent').html(`
                <p><strong>Booking Code:</strong> ${res.BookingToken}</p>
                <p><strong>Guest Name:</strong> ${res.GuestFirstName} ${res.GuestLastName}</p>
                <p><strong>Email:</strong> ${res.GuestEmail}</p>
                <p><strong>Payment:</strong> $${res.PaymentAmount || '0.00'} (${res.PaymentMethod || 'N/A'})</p>
                <p><strong>Payment Status:</strong> ${res.PaymentStatus || 'N/A'}</p>
                <p><strong>Rooms:</strong></p>
                ${roomsHtml}
            `);

            $('#detailsModal').show();
        },
        error: function() {
            alert('Failed to load reservation details.');
        }
    });
});

function closeModal() {
    $('#detailsModal').hide();
}

function fetchLiveReservations() {
    $.ajax({
        url: '/admin/live-reservations',
        method: 'GET',
        dataType: 'json',
        success: function(reservations) {
            let feedHtml = '';
            if (reservations.length === 0) {
                feedHtml = '<p class="text-gray-500">No pending reservations.</p>';
            } else {
                reservations.forEach(res => {
                    feedHtml += `
                        <div class="p-2 border border-gray-200 rounded flex justify-between items-center hover:bg-gray-50">
                            <div>
                                <p class="font-semibold">${res.GuestFirstName} ${res.GuestLastName}</p>
                                <p class="text-sm text-gray-600">Booking: ${res.BookingToken}</p>
                                <p class="text-sm text-gray-600">Room ${res.RoomNumber} (${res.RoomTypeName})</p>
                                <p class="text-sm text-gray-600">Check-in: ${res.CheckInDate} | Check-out: ${res.CheckOutDate}</p>
                                <p class="text-sm text-gray-600">Guests: ${res.NumAdults + res.NumChildren}</p>
                                <p class="text-sm text-gray-600">Payment: ${res.PaymentStatus || 'Pending'} (${res.PaymentMethod || 'N/A'})</p>
                            </div>
                            <div>
                                <button onclick="openDetailsModal('<strong>${res.GuestFirstName} ${res.GuestLastName}</strong> - Booking: ${res.BookingToken}')" 
                                    class="bg-blue-500 text-white px-2 py-1 rounded text-sm">View</button>
                                <button onclick="confirmCancel(${res.ReservationID})" 
                                    class="bg-red-500 text-white px-2 py-1 rounded text-sm">Cancel</button>
                            </div>
                        </div>
                    `;
                });
            }
            $('#liveFeed').html(feedHtml);
        },
        error: function(err) {
            console.error('Error fetching live reservations:', err);
        }
    });
}

// Poll every 5 seconds
setInterval(fetchLiveReservations, 5000);
fetchLiveReservations(); // initial load

function confirmCancel(reservationId) {
    if (!confirm('Are you sure you want to cancel this reservation?')) return;

    $.ajax({
        url: '/admin/reservations/cancel', // route to your cancel handler
        method: 'POST',
        data: { ReservationID: reservationId },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                alert('Reservation canceled.');
                fetchLiveReservations(); // refresh feed
            } else {
                alert('Failed: ' + resp.message);
            }
        },
        error: function() {
            alert('Server error while canceling reservation.');
        }
    });
}