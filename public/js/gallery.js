document.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector("#gallery .grid"); // updated selector
  const images = Array.from(grid.querySelectorAll("img"));

  const leftArrow = document.querySelector("#gallery .left-0");
  const rightArrow = document.querySelector("#gallery .right-0");
  const leftBg = leftArrow.querySelector(".arrow-bg");
  const rightBg = rightArrow.querySelector(".arrow-bg");

  let currentPage = 0;
  const perPage = 8;

  function updateArrows() {
    // LEFT ARROW (disabled if on first page)
    if (currentPage === 0) {
      leftBg.classList.remove("bg-zinc-300/90");
      leftBg.classList.add("bg-zinc-800/90");
      leftArrow.classList.add("pointer-events-none", "opacity-50");
    } else {
      leftBg.classList.remove("bg-zinc-800/90");
      leftBg.classList.add("bg-zinc-300/90");
      leftArrow.classList.remove("pointer-events-none", "opacity-50");
    }

    // RIGHT ARROW (disabled if on last page)
    if ((currentPage + 1) * perPage >= images.length) {
      rightBg.classList.remove("bg-zinc-300/90");
      rightBg.classList.add("bg-zinc-800/90");
      rightArrow.classList.add("pointer-events-none", "opacity-50");
    } else {
      rightBg.classList.remove("bg-zinc-800/90");
      rightBg.classList.add("bg-zinc-300/90");
      rightArrow.classList.remove("pointer-events-none", "opacity-50");
    }
  }

  function renderPage() {
    images.forEach((img, i) => {
      const start = currentPage * perPage;
      const end = start + perPage;
      img.parentElement.style.display =
        (i >= start && i < end) ? "block" : "none";
    });
    updateArrows();
  }

  // Arrow navigation
  leftArrow.addEventListener("click", () => {
    if (currentPage > 0) {
      currentPage--;
      renderPage();
    }
  });

  rightArrow.addEventListener("click", () => {
    if ((currentPage + 1) * perPage < images.length) {
      currentPage++;
      renderPage();
    }
  });

  renderPage();

  // --- Popup logic ---
  images.forEach(img => {
    img.style.cursor = "pointer"; // makes it obvious it's clickable

    img.addEventListener("click", () => {
      const popup = document.createElement("div");
      popup.className = "fixed inset-0 bg-black/80 flex items-center justify-center z-50";

      const popupImg = document.createElement("img");
      popupImg.src = img.src;
      popupImg.alt = img.alt;
      popupImg.className = "max-w-[90%] max-h-[90%] rounded shadow-lg";

      popup.appendChild(popupImg);
      document.body.appendChild(popup);

      // Close on click outside
      popup.addEventListener("click", (e) => {
        if (e.target === popup) {
          popup.remove();
        }
      });
    });
  });
});
