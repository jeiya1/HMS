const testimonials = [
    {
        text: "The hotel exceeded my expectations in so many ways. The deluxe room was spacious and stylish.",
        author: "Shan L."
    },
    {
        text: "Amazing service and very relaxing atmosphere. The pool area was my favorite spot!",
        author: "Maria K."
    },
    {
        text: "Clean rooms, friendly staff, and great food. Highly recommended for travelers.",
        author: "James T."
    }
];

let index = 0;

setInterval(() => {
    const textEl = document.getElementById("testimonial-text");
    const authorEl = document.getElementById("testimonial-author");

    textEl.classList.remove("opacity-100");
    textEl.classList.add("opacity-0");

    setTimeout(() => {
        index = (index + 1) % testimonials.length;

        textEl.innerText = testimonials[index].text;
        authorEl.innerText = testimonials[index].author + " —";

        textEl.classList.remove("opacity-0");
        textEl.classList.add("opacity-100");
    }, 300);
}, 4000);