const settingsBtn   = document.getElementById('settings-btn');
const settingsPopup = document.getElementById('settings-popup');
const overlay       = document.getElementById('overlay');
const closeSettings = document.getElementById('close-settings');

// ── Open / close settings popup ──────────────────────────────────────────────
settingsBtn.addEventListener('click', () => {
    overlay.classList.remove('hidden');
    settingsPopup.classList.remove('hidden');
    setTimeout(() => {
        settingsPopup.classList.remove('scale-0');
        settingsPopup.classList.add('scale-100');
    }, 10);
});

function closeSettingsPopup() {
    settingsPopup.classList.remove('scale-100');
    settingsPopup.classList.add('scale-0');
    setTimeout(() => {
        settingsPopup.classList.add('hidden');
        overlay.classList.add('hidden');
    }, 300);
}

closeSettings.addEventListener('click', closeSettingsPopup);
overlay.addEventListener('click', closeSettingsPopup);

// ── Logout popup ──────────────────────────────────────────────────────────────
function openLogoutPopup() {
    document.getElementById('logout-overlay').classList.remove('hidden');
    document.getElementById('logout-popup').classList.remove('hidden');
}

function closeLogoutPopup() {
    document.getElementById('logout-overlay').classList.add('hidden');
    document.getElementById('logout-popup').classList.add('hidden');
}

document.getElementById('logout-btn').addEventListener('click', () => {
    closeSettingsPopup(); // close settings first
    setTimeout(openLogoutPopup, 320); // wait for settings close animation
});

document.getElementById('cancel-logout').addEventListener('click', closeLogoutPopup);

// ── jQuery handlers ───────────────────────────────────────────────────────────
$(document).ready(function () {

    // ── CONFIRM button: handles BOTH username and password in one handler ──────
    // BUG FIX: previously two separate #confirm-btn handlers were bound —
    // only the first one ever fired. Now merged into one.
    $('#confirm-btn').on('click', function () {
        const newUsername     = $('#settings-popup input[type="text"]').val().trim();
        const currentPassword = $('#settings-popup input[type="password"]').first().val().trim();
        const newPassword     = $('#settings-popup input[type="password"]').last().val().trim();

        const hasUsername = newUsername !== '';
        const hasPassword = currentPassword !== '' || newPassword !== '';

        // Require at least one change
        if (!hasUsername && !hasPassword) {
            showToast('Please enter a new username or password to update.', 'error');
            return;
        }

        // Validate password fields together if either is filled
        if (hasPassword && (!currentPassword || !newPassword)) {
            showToast('Please fill in both current and new password fields.', 'error');
            return;
        }

        // ── Username change ───────────────────────────────────────────────────
        if (hasUsername) {
            $.ajax({
                url:    '/admin/username-change',
                method: 'POST',
                data:   { username: newUsername },
                success: function (res) {
                    if (res.success) {
                        showToast(res.message || 'Username updated successfully.', 'success');
                        // Update displayed name in the header
                        $('header p.font-bold').text(newUsername);
                        $('#settings-popup span.text-xl').text(newUsername);
                        $('#settings-popup input[type="text"]').val('');
                    } else {
                        showToast(res.message || 'Failed to update username.', 'error');
                    }
                },
                error: function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to update username.', 'error');
                }
            });
        }

        // ── Password change ───────────────────────────────────────────────────
        if (hasPassword) {
            $.ajax({
                url:    '/admin/password-change',
                method: 'POST',
                data:   { current_password: currentPassword, new_password: newPassword },
                success: function (res) {
                    if (res.success) {
                        showToast(res.message || 'Password updated successfully.', 'success');
                        $('#settings-popup input[type="password"]').val('');
                    } else {
                        showToast(res.message || 'Failed to update password.', 'error');
                    }
                },
                error: function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to update password.', 'error');
                }
            });
        }
    });

    // ── Logout confirm ────────────────────────────────────────────────────────
    $('#confirm-logout').on('click', function () {
        $.ajax({
            url:      '/admin/logout',
            method:   'POST',
            dataType: 'json',
            success: function () {
                window.location.href = '/admin/login';
            },
            error: function () {
                showToast('Logout failed.', 'error');
            }
        });
    });

});