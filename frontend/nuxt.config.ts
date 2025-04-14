// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: "2024-11-01",
    devtools: { enabled: true },
    modules: [
      "@nuxt/eslint",
      "@nuxt/image",
      "@nuxtjs/tailwindcss",
      "@pinia/nuxt",
      "pinia-plugin-persistedstate/nuxt",
      "@nuxtjs/color-mode",
      "@nuxtjs/google-fonts"
    ],
    tailwindcss: {
        exposeConfig: true,
        viewer: true,
    },
    runtimeConfig: {
        public: {
            apiUrl: "",
        },
    },
    colorMode: {
        preference: "system",
        fallback: "light",
        classSuffix: '',
    },
    vite: {
        server: {
            allowedHosts: ['dev.nassimlounadi.fr']
        }
    },
    components: {
        dirs: [
            {
                path: '~/components',
                pathPrefix: false
            }
        ]
    },
    googleFonts: {
        download: true,
        preload: true,
        families: {
            Poppins: {
                wght: '100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900',
                ital: '100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900'
            }
        }
    }
});