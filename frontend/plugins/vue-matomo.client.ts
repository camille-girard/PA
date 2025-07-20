import { defineNuxtPlugin } from '#app';
import VueMatomo from 'vue-matomo';

export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig();
    nuxtApp.vueApp.use(VueMatomo, {
        host: config.public.matomoUrl  || 'http://localhost:8082',
        siteId: 1,
    });
});
