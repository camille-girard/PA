import { defineNuxtPlugin } from '#app';
import VueMatomo from 'vue-matomo';

export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig();
    nuxtApp.vueApp.use(VueMatomo, {
        host: config.public.matomoUrl  || 'https://matomo.popnbed.com',
        siteId: 1,
    });
});
