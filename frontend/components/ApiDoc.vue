<template>
    <div class="swagger-container">
        <div class="swagger-header">
            <h1>API Documentation</h1>
            <div class="theme-toggle">
                <button class="theme-button" @click="toggleTheme">
                    {{ isDarkMode ? '☀️' : '🌙' }}
                </button>
            </div>
        </div>
        <div id="swagger-ui"></div>
    </div>
</template>

<script setup lang="ts">
    import { onMounted, ref, watch } from 'vue';
    import SwaggerUI from 'swagger-ui-dist/swagger-ui-bundle.js';
    import 'swagger-ui-dist/swagger-ui.css';

    const colorMode = useColorMode();
    const isDarkMode = ref(colorMode.value === 'dark');
    const swaggerUIInstance = ref(null);

    const props = defineProps({
        url: {
            type: String,
            default: 'http://localhost:3000/api/doc.json',
        },
    });

    const toggleTheme = () => {
        colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark';
        isDarkMode.value = colorMode.value === 'dark';
        applyTheme();
    };

    const applyTheme = () => {
        const swaggerContainer = document.querySelector('.swagger-ui');
        if (swaggerContainer) {
            if (isDarkMode.value) {
                swaggerContainer.classList.add('swagger-dark');
            } else {
                swaggerContainer.classList.remove('swagger-dark');
            }
        }
    };

    const initSwaggerUI = () => {
        swaggerUIInstance.value = SwaggerUI({
            dom_id: '#swagger-ui',
            url: props.url,
            deepLinking: true,
            docExpansion: 'list',
            filter: true,
            syntaxHighlight: {
                activate: true,
                theme: 'agate',
            },
            presets: [SwaggerUI.presets.apis],
            layout: 'BaseLayout',
            supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
            onComplete: () => {
                applyTheme();
            },
        });
    };

    onMounted(() => {
        initSwaggerUI();
    });

    // Surveiller les changements d'URL pour actualiser l'UI
    watch(
        () => props.url,
        (newUrl) => {
            if (swaggerUIInstance.value) {
                swaggerUIInstance.value.specActions.updateUrl(newUrl);
            }
        }
    );

    // Surveiller les changements de thème
    watch(
        () => colorMode.value,
        () => {
            isDarkMode.value = colorMode.value === 'dark';
            applyTheme();
        }
    );
</script>

<style>
    .swagger-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }

    .swagger-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .dark .swagger-header {
        background-color: #343a40;
        color: white;
        border-bottom: 1px solid #495057;
    }

    .theme-button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    #swagger-ui {
        height: 100%;
        flex-grow: 1;
        overflow: auto;
    }

    /* Styles pour le mode sombre */
    .swagger-ui.swagger-dark {
        filter: invert(88%) hue-rotate(180deg);
    }

    .swagger-ui.swagger-dark img {
        filter: invert(100%) hue-rotate(180deg);
    }

    /* Personnalisation des boutons d'opération */
    .swagger-ui .opblock-summary-method {
        border-radius: 4px;
        font-weight: 600;
    }

    /* Amélioration de la lisibilité des titres */
    .swagger-ui .opblock-tag {
        font-size: 1.2rem !important;
        font-weight: 600 !important;
    }

    /* Arrondir les blocs */
    .swagger-ui .opblock {
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
</style>
