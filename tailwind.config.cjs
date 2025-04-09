/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './resources/**/*.ts',
    './resources/**/*.jsx',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1D4ED8', // Custom primary color
        secondary: '#9333EA', // Custom secondary color
      },
      spacing: {
        '128': '32rem', // Custom spacing
        '144': '36rem', // Custom spacing
      },
      typography: (theme) => ({
        DEFAULT: {
          css: {
            color: theme('colors.gray.700'),
            a: {
              color: theme('colors.primary'),
              '&:hover': {
                color: theme('colors.secondary'),
              },
            },
          },
        },
      }),
    },
  },
  darkMode: 'class', // Enables dark mode based on a CSS class
  plugins: [
    require('@tailwindcss/typography'), // Adds typography plugin
    require('@tailwindcss/forms'), // Adds forms plugin
  ],
};
