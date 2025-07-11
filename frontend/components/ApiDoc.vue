<script setup lang="ts">
    // import { onMounted, ref, watch } from 'vue';
    // import 'swagger-ui-dist/swagger-ui.css';

    // const colorMode = useColorMode();
    // const isDarkMode = ref(colorMode.value === 'dark');
    // const swaggerUIInstance = ref(null);

    // const props = defineProps({
    //     url: {
    //         type: String,
    //         default: 'http://localhost:3000/api/doc.json',
    //     },
    // });

    // // Renommé avec underscore car non utilisé
    // const _toggleTheme = () => {
    //     colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark';
    //     isDarkMode.value = colorMode.value === 'dark';
    //     applyTheme();
    // };

    // const applyTheme = () => {
    //     const swaggerContainer = document.querySelector('.swagger-ui');
    //     if (swaggerContainer) {
    //         if (isDarkMode.value) {
    //             swaggerContainer.classList.add('swagger-dark');
    //         } else {
    //             swaggerContainer.classList.remove('swagger-dark');
    //         }
    //     }
    // };

    // const initSwaggerUI = () => {
    //     swaggerUIInstance.value = SwaggerUI({
    //         dom_id: '#swagger-ui',
    //         url: props.url,
    //         deepLinking: true,
    //         docExpansion: 'list',
    //         filter: true,
    //         syntaxHighlight: {
    //             activate: true,
    //             theme: 'agate',
    //         },
    //         presets: [SwaggerUI.presets.apis],
    //         layout: 'BaseLayout',
    //         supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
    //         onComplete: () => {
    //             applyTheme();
    //         },
    //     });
    // };

    // onMounted(() => {
    //     initSwaggerUI();
    // });

    // watch(
    //     () => props.url,
    //     (newUrl) => {
    //         if (swaggerUIInstance.value) {
    //             swaggerUIInstance.value.specActions.updateUrl(newUrl);
    //         }
    //     }
    // );

    // watch(
    //     () => colorMode.value,
    //     () => {
    //         isDarkMode.value = colorMode.value === 'dark';
    //         applyTheme();
    //     }
    // );
</script>
<template>
    <div class="swagger-container">
        <div id="swagger-ui"></div>
    </div>
</template>
