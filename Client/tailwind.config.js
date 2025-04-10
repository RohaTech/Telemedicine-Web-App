/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        "first-accent": "#048581",
        "second-accent": "#2FC7A1"
      }
    },
  },
  plugins: [],
}

