export interface AccommodationImage {
    id: number;
    url: string;
    alt?: string;
    isMain: boolean;
}

export const useAccommodationImages = (accommodationId: number) => {
    const uploading = ref(false);
    const deleting = ref(false);
    const settingMain = ref(false);

    const uploadImages = async (files: File[]): Promise<AccommodationImage[]> => {
        if (files.length === 0) return [];

        uploading.value = true;

        try {
            const formData = new FormData();
            files.forEach((file, index) => {
                formData.append(`image_${index}`, file);
            });

            const response = await $fetch(`/api/accommodations/${accommodationId}/images`, {
                method: 'POST',
                body: formData,
            });

            return response.images || [];
        } catch (error) {
            console.error("Erreur lors de l'upload des images:", error);
            throw error;
        } finally {
            uploading.value = false;
        }
    };

    const deleteImage = async (imageId: number): Promise<void> => {
        deleting.value = true;

        try {
            await $fetch(`/api/accommodations/${accommodationId}/images/${imageId}`, {
                method: 'DELETE',
            });
        } catch (error) {
            console.error("Erreur lors de la suppression de l'image:", error);
            throw error;
        } finally {
            deleting.value = false;
        }
    };

    const setMainImage = async (imageId: number): Promise<void> => {
        settingMain.value = true;

        try {
            await $fetch(`/api/accommodations/${accommodationId}/images/${imageId}/main`, {
                method: 'PUT',
            });
        } catch (error) {
            console.error("Erreur lors de la définition de l'image principale:", error);
            throw error;
        } finally {
            settingMain.value = false;
        }
    };

    return {
        uploading: readonly(uploading),
        deleting: readonly(deleting),
        settingMain: readonly(settingMain),
        uploadImages,
        deleteImage,
        setMainImage,
    };
};
