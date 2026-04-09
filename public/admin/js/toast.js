function showToast(message, type = "error") {
    const container = document.getElementById("toast-container");

    const toast = document.createElement("div");
    toast.className = `
        relative max-w-xs w-full px-4 py-3 rounded-lg shadow-lg
        text-sm flex items-start gap-3
        bg-[#111827] text-[#E5E7EB] border-l-4 border-[#4D5765]
        opacity-0 translate-x-10 transition-all duration-500
    `;

    // Message
    const msg = document.createElement("div");
    msg.className = "flex-1 pr-5";
    msg.innerText = message;

    // Close button (X)
    const closeBtn = document.createElement("button");
    closeBtn.innerHTML = "&times;";
    closeBtn.className = `
        absolute top-0 right-2 text-[#E5E7EB] text-lg font-bold
        hover:text-white transition cursor-pointer
    `;

    closeBtn.onclick = () => {
        toast.classList.add("opacity-0", "translate-x-10");
        toast.addEventListener("transitionend", () => toast.remove());
    };

    toast.appendChild(msg);
    toast.appendChild(closeBtn);
    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove("opacity-0", "translate-x-10");
    });

    // Auto remove after 1 min
    setTimeout(() => {
        toast.classList.add("opacity-0", "translate-x-10");
        toast.addEventListener("transitionend", () => toast.remove());
    }, 15000);
}