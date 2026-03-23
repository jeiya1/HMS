// Select all thumbnail images
const thumbnails = document.querySelectorAll('.left-thumbnails img');
// Select the main large image
const mainImage = document.querySelector('.main-image img');

thumbnails.forEach(thumbnail => {
    thumbnail.addEventListener('click', () => {
        // Update the src of the main image
        mainImage.src = thumbnail.src;
    });
});