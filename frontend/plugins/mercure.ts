export default defineNuxtPlugin(() => {
    // This plugin is used to setup the Mercure hub URL
    // The URL will be injected via environment variable in production
    // For local development, we use the URL defined in the docker-compose file
    const config = useRuntimeConfig();

    return {
        provide: {
            mercureUrl: config.public.mercureUrl || 'http://localhost:1337/.well-known/mercure',
        },
    };
});
