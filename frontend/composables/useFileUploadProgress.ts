export function useFileUploadProgress() {
    const progress = ref(0);
    const isUploading = ref(false);
    const isSuccess = ref(false);
    const isError = ref(false);
    const responseData = ref<unknown>(null);
    const statusCode = ref();

    const upload = (file: File, url: string, fieldName: string = 'file') => {
        return new Promise<void>((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            formData.append(fieldName, file);

            isUploading.value = true;
            isSuccess.value = false;
            isError.value = false;
            progress.value = 0;
            responseData.value = null;
            statusCode.value = null;

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    progress.value = Math.round((e.loaded / e.total) * 100);
                }
            };

            xhr.onload = () => {
                isUploading.value = false;
                statusCode.value = xhr.status;

                if (xhr.status >= 200 && xhr.status < 300) {
                    isSuccess.value = true;
                    try {
                        responseData.value = JSON.parse(xhr.responseText);
                    } catch {
                        responseData.value = xhr.responseText;
                    }
                    resolve();
                } else {
                    isError.value = true;
                    reject(xhr.responseText);
                }
            };

            xhr.onerror = () => {
                isUploading.value = false;
                isError.value = true;
                reject(xhr.statusText);
            };

            xhr.open('POST', url);
            xhr.withCredentials = true; // Include cookies for authentication
            xhr.send(formData);
        });
    };

    return { upload, progress, isUploading, isSuccess, isError, responseData, statusCode };
}
