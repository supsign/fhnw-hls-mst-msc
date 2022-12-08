const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: ['./resources/**/*.{js,ts,jsx,tsx,blade.php,vue}'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                hls: {
                    DEFAULT: '#FDE70E',
                    200: '#fef387',
                    700: '#ead20a',
                },
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
        },
    },
    plugins: [],
};
