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
