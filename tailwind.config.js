export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#174DAD",
                primaryDark: "#3883CE",
                primaryLight: "#5EA8E5",
                accent: "#E8FE61",
                darkBg: "#12002E",
                lightBg: "#EEEEEE",
                textColor: "#202020",
            },
            fontFamily: {
                arabic: ['"Noto Kufi Arabic"', "sans-serif"],
                display: ['"Lemonada"', "cursive"],
            },
            keyframes: {
                marquee: {
                    "0%": { transform: "translateX(0)" },
                    "100%": { transform: "translateX(50%)" },
                },

                "marquee-ltr": {
                    "0%": { transform: "translateX(0)" },
                    "100%": { transform: "translateX(-50%)" },
                },

                "marquee-partners": {
                    "0%": { transform: "translateX(0)" },
                    "100%": { transform: "translateX(50%)" },
                },

                "marquee-partners-ltr": {
                    "0%": { transform: "translateX(0)" },
                    "100%": { transform: "translateX(-50%)" },
                },
            },
            animation: {
                marquee: "marquee 22s linear infinite",
                "marquee-ltr": "marquee-ltr 22s linear infinite",
                "marquee-partners": "marquee-partners 28s linear infinite",
                "marquee-partners-ltr": "marquee-partners-ltr 28s linear infinite",
            },
            screens: {
                'ipad-mini': {'min': '768px', 'max': '1023px'},
                'ipad-mini-land': {'min': '1024px', 'max': '1179px', 'orientation': 'landscape'},
            },
        },
    },
    plugins: [],
};
