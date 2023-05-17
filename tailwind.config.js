module.exports = {
  content: ['./resources/**/*.{js,ts,jsx,tsx,blade.php,vue}'],
  theme: {
    extend: {
      colors: {
        hls: {
          DEFAULT: '#FDE70E',
          200: '#fef387',
          700: '#ead20a'
        },
        primary: '#fde70e',
        light: '#deded9',
        dark: '#4c4c4c'
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
