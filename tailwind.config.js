// tailwind.config.js
module.exports = {
  darkMode: 'class',
  content: [
    "./index.php",
    "./*.{html,js,php}",
    "./**/*.{html,js,php}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          '50': '#f5f3ff',
          '100': '#ede9fe',
          '200': '#ddd6fe',
          '300': '#c4b5fd',
          '400': '#a78bfa',
          '500': '#8b5cf6',
          '600': '#7c3aed',
          '700': '#6d28d9',
          '800': '#5b21b6',
          '900': '#4c1d95',
        },
        secondary: '#5b21b6',
        accent: '#F59E0B',
      }
    }
  },
  plugins: [],
}
