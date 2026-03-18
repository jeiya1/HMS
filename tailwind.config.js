/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/views/**/*.php",
    "./app/views/**/*.html",
    "./public/**/*.html",
    "./public/**/*.php",
    "./public/js/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        crimson: ['"Crimson Text"', 'serif'],
      },
    },
  },
  plugins: [],
};

