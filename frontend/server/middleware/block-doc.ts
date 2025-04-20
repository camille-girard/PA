export default defineEventHandler((event) => {
    if (process.env.NODE_ENV !== 'production') return;

    const url = getRequestURL(event);
    if (url.pathname.startsWith('/doc')) {
        throw createError({
            statusCode: 404,
            statusMessage: 'Page Not Found',
        });
    }
});
