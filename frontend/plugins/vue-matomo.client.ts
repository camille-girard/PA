import { defineNuxtPlugin } from '#app';
import VueMatomo from 'vue-matomo';

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.vueApp.use(VueMatomo, {
        host: import.meta.env.MATOMO_URL || 'http://localhost:8082',
        siteId: 1,
    });
});
