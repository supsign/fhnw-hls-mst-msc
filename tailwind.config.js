const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: ['./resources/**/*.{js,ts,jsx,tsx,blade.php,vue}'],
  theme: {
    extend: {
      colors: {
        hls: {
          DEFAULT: '#FDE70E',
          200: '#fef387',
          700: '#ead20a'
        }
      },
      padding: {
        '15': '60px'
      }
    }
  },
  variants: {
    extend: {
      backgroundColor: ['active']
    }
  },
  plugins: []
};
