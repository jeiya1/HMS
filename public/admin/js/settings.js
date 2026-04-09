const settingsBtn = document.getElementById('settings-btn');
const settingsPopup = document.getElementById('settings-popup');
const overlay = document.getElementById('overlay');
const closeSettings = document.getElementById('close-settings');

settingsBtn.addEventListener('click', () => {
    overlay.classList.remove('hidden');
    settingsPopup.classList.remove('hidden');
    setTimeout(() => {
        settingsPopup.classList.remove('scale-0');
        settingsPopup.classList.add('scale-100');
    }, 10);
});

closeSettings.addEventListener('click', () => {
    settingsPopup.classList.remove('scale-100');
    settingsPopup.classList.add('scale-0');
    setTimeout(() => {
        settingsPopup.classList.add('hidden');
        overlay.classList.add('hidden');
    }, 300);
});

overlay.addEventListener('click', () => {
    closeSettings.click();
});

document.getElementById("logout-btn").addEventListener("click", () => {
    document.getElementById("logout-popup").classList.remove("hidden");
});

document.getElementById("cancel-logout").addEventListener("click", () => {
    document.getElementById("logout-popup").classList.add("hidden");
});

document.getElementById("confirm-logout").addEventListener("click", () => {
});

$(document).ready(function () {

    // ----------------------------
    // Change Username
    // ----------------------------
    $('#confirm-btn').on('click', function () {
        const newUsername = $('#settings-popup input[type="text"]').val().trim();

        if (!newUsername) {
            showToast("Username cannot be empty");
            return;
        }

        $.ajax({
            url: '/admin/username-change',
            method: 'POST',
            data: { username: newUsername },
            success: function (res) {
                showToast(res.message || "Username updated successfully");
                // Optionally update displayed username
                $('header p.font-bold').text(newUsername);
                $('#settings-popup').addClass('scale-0 hidden');
                $('#overlay').addClass('hidden');
            },
            error: function (xhr) {
                showToast(xhr.responseJSON?.message || "Failed to update username");
            }
        });
    });

    // ----------------------------
    // Change Password
    // ----------------------------
    $('#confirm-btn').on('click', function () {
        const currentPassword = $('#settings-popup input[type="password"]').first().val().trim();
        const newPassword = $('#settings-popup input[type="password"]').last().val().trim();

        if (!currentPassword || !newPassword) {
            showToast("Please fill both password fields");
            return;
        }

        $.ajax({
            url: '/admin/password-change',
            method: 'POST',
            data: { current_password: currentPassword, new_password: newPassword },
            success: function (res) {
                showToast(res.message || "Password updated successfully");
                $('#settings-popup').addClass('scale-0 hidden');
                $('#overlay').addClass('hidden');
                // Clear password fields
                $('#settings-popup input[type="password"]').val('');
            },
            error: function (xhr) {
                showToast(xhr.responseJSON?.message || "Failed to update password");
            }
        });
    });

    // ----------------------------
    // Logout
    // ----------------------------
    $('#confirm-logout').on('click', function () {
        $.ajax({
            url: '/admin/logout',
            method: 'POST',
            dataType: 'json',
            success: function () {
                window.location.href = '/admin/login';
            },
            error: function () {
                showToast("Logout failed");
            }
        });
    });

    // Cancel logout
    $('#cancel-logout').on('click', function () {
        $('#logout-popup').addClass('hidden');
        $('#logout-overlay').addClass('hidden');
    });

});