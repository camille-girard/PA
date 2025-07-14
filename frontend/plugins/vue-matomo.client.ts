import { defineNuxtPlugin } from '#app';
import VueMatomo from 'vue-matomo';

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.vueApp.use(VueMatomo, {
        host: 'http://localhost:8082',
        siteId: 1,
    });
});
