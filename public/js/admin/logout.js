const logoutBtn = document.getElementById("confirm-logout");
const logoutPopup = document.getElementById("logout-popup");
const logoutOverlay = document.getElementById("logout-overlay");
const cancelLogout = document.getElementById("cancel-logout");

function openLogoutPopup() {
    logoutOverlay.classList.remove("hidden");
    logoutPopup.classList.remove("hidden");
}

function closeLogoutPopup() {
    logoutOverlay.classList.add("hidden");
    logoutPopup.classList.add("hidden");
}

cancelLogout.addEventListener("click", closeLogoutPopup);

// Example: trigger logout popup
document.getElementById("logout-btn").addEventListener("click", openLogoutPopup);