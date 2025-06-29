// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2024-11-01',
    devtools: { enabled: true },
    modules: [
        '@nuxt/eslint',
        '@nuxt/image',
        '@nuxtjs/tailwindcss',
        '@pinia/nuxt',
        'pinia-plugin-persistedstate/nuxt',
        '@nuxtjs/color-mode',
        '@nuxtjs/google-fonts',
        '@nuxtjs/i18n',
        '@nuxtjs/sitemap',
    ],

    i18n: {
        locales: [
            { code: 'fr', language: 'fr-FR' },
            { code: 'en', language: 'en-US' },
            { code: 'es', language: 'es-ES' },
        ],
        defaultLocale: 'fr',
        bundle: {
            optimizeTranslationDirective: false,
        },
    },

    tailwindcss: {
        exposeConfig: true,
        viewer: true,
    },

    runtimeConfig: {
        public: {
            apiUrl: process.env.NUXT_PUBLIC_API_URL,
            mapboxToken: process.env.MAPBOX_TOKEN,
        },
    },

    build: {
        transpile: ['swagger-ui-dist'],
    },

    vite: {
        server: {
            allowedHosts: ['popnbed.com'],
        },
        optimizeDeps: {
            include: ['swagger-ui-dist/swagger-ui-bundle.js'],
        },
    },

    components: {
        dirs: [
            {
                path: '~/components',
                pathPrefix: false,
            },
        ],
    },

    googleFonts: {
        download: true,
        preload: true,
        families: {
            Poppins: {
                wght: '100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900',
                ital: '100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
            },
        },
    },

    nitro: {
        compressPublicAssets: true,
    },
});
