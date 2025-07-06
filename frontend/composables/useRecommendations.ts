export const useRecommendations = () => {
    const { $api } = useNuxtApp();

    const getPersonalizedRecommendations = async () => {
        try {
            const { data, error } = await useAuthFetch($api('/api/recommendations'), {
                method: 'GET',
                credentials: 'include',
            });

            if (error.value) {
                console.error('Error fetching recommendations:', error.value);
                return [];
            }

            return data.value || [];
        } catch (error) {
            console.error('Error fetching recommendations:', error);
            return [];
        }
    };

    return {
        getPersonalizedRecommendations,
    };
};
