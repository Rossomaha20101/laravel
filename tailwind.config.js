// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  purge: [
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.bjs',
    './resources/**/*.vue',
  ],
  darkMode: false,
  theme: {
    extend: {},
  },
  variants: {
    extend: []
  },
  plugins: [],
}