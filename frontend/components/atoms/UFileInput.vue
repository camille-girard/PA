<script setup lang="ts">
    import SVGIcon from './icons/SVGIcon.vue';
    import JPGIcon from '~/components/atoms/icons/JPGIcon.vue';
    import JPEGIcon from '~/components/atoms/icons/JPEGIcon.vue';
    import PNGIcon from '~/components/atoms/icons/PNGIcon.vue';
    import IMGIcon from '~/components/atoms/icons/IMGIcon.vue';
    import PDFIcon from '~/components/atoms/icons/PDFIcon.vue';
    import InfoCircleIcon from '~/components/atoms/icons/InfoCircleIcon.vue';

    interface FileInputProps {
        accept?: string;
        maxSizeText?: string;
        label?: string;
        dragText?: string;
        formatText?: string;
        multiple?: boolean;
        uploadUrl?: string;
    }

    const _props = withDefaults(defineProps<FileInputProps>(), {
        accept: 'image/svg+xml, image/png, image/jpeg, image/gif',
        maxSizeText: '(max. 800x400px)',
        label: 'Click to upload',
        dragText: 'or drag and drop',
        formatText: 'SVG, PNG, JPG or GIF',
        multiple: false,
        uploadUrl: '',
    });

    const emit = defineEmits(['update:file', 'update:progress', 'upload:success', 'upload:error']);

    const fileInput = ref<HTMLInputElement | null>(null);
    const isDragging = ref(false);
    const selectedFile = ref<File | null>(null);
    const selectedFiles = ref<File[]>([]);

    const fileIds = ref(new Map<File, string>());
    const fileProgresses = ref<Map<string, { progress: number; currentSize: number; totalSize: number }>>(new Map());

    // Use the real upload functionality
    const { upload, progress, isUploading, isSuccess, isError, responseData } = useFileUploadProgress();

    const handleFileUpload = async (file: File) => {
        const fileId = `${file.name}-${file.size}-${Date.now()}`;
        fileIds.value.set(file, fileId);

        fileProgresses.value.set(fileId, {
            progress: 0,
            currentSize: 0,
            totalSize: file.size,
        });

        // Always emit the update:file event so parent components can get the file even if uploadUrl is not set
        emit('update:file', file);

        if (_props.uploadUrl && _props.uploadUrl.trim() !== '') {
            try {
                // Start the real upload
                await upload(file, _props.uploadUrl);

                // Update the file progress based on the upload progress
                const updateProgress = () => {
                    const fileData = fileProgresses.value.get(fileId);
                    if (fileData) {
                        fileData.progress = progress.value;
                        fileData.currentSize = Math.floor((progress.value / 100) * file.size);
                        fileProgresses.value.set(fileId, fileData);
                        emit('update:progress', { fileId, progress: progress.value });
                    }
                };

                // Create an interval to update progress
                const progressInterval = setInterval(() => {
                    updateProgress();

                    // When upload is complete or has error, clear the interval
                    if (!isUploading.value) {
                        updateProgress(); // Ensure final progress is set
                        clearInterval(progressInterval);

                        if (isSuccess.value) {
                            emit('upload:success', { file, response: responseData.value });
                        } else if (isError.value) {
                            emit('upload:error', { file, error: 'Upload failed' });
                        }
                    }
                }, 100);
            } catch (error) {
                // Handle upload errors
                const fileData = fileProgresses.value.get(fileId);
                if (fileData) {
                    fileData.progress = 0;
                    fileProgresses.value.set(fileId, fileData);
                }
                emit('upload:error', { file, error });
            }
        } else {
            // If no uploadUrl provided, store the file but don't upload
            // Just update the UI to show "Ready for upload"
            const fileData = fileProgresses.value.get(fileId);
            if (fileData) {
                fileData.progress = 0; // Show 0% progress since we're not actually uploading yet
                fileProgresses.value.set(fileId, fileData);
                emit('update:progress', { fileId, progress: 0 });
            }

            // We're not actually uploading the file, just storing it
            // The parent component will handle the actual upload when needed
            console.info(
                'No uploadUrl provided for UFileInput. File will be passed to parent component for later upload.'
            );
        }
    };

    const handleFileSelect = (event: Event) => {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files.length > 0) {
            if (_props.multiple) {
                selectedFiles.value = Array.from(input.files);
                emit('update:file', selectedFiles.value);

                selectedFiles.value.forEach((file) => {
                    handleFileUpload(file);
                });
            } else {
                selectedFile.value = input.files[0];
                emit('update:file', selectedFile.value);
                handleFileUpload(selectedFile.value);
            }
        }
    };

    const handleClick = () => {
        fileInput.value?.click();
    };

    const handleDragEnter = (e: DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        isDragging.value = true;
    };

    const handleDragLeave = (e: DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        isDragging.value = false;
    };

    const handleDragOver = (e: DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.dataTransfer) {
            e.dataTransfer.dropEffect = 'copy';
        }
    };

    const handleDrop = (e: DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        isDragging.value = false;

        if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
            if (_props.multiple) {
                selectedFiles.value = Array.from(e.dataTransfer.files);
                emit('update:file', selectedFiles.value);

                selectedFiles.value.forEach((file) => {
                    handleFileUpload(file);
                });
            } else {
                selectedFile.value = e.dataTransfer.files[0];
                emit('update:file', selectedFile.value);
                handleFileUpload(selectedFile.value);
            }
        }
    };

    const getIconForFile = (file: File) => {
        const extension = file.name.split('.').pop()?.toLowerCase();

        switch (extension) {
            case 'svg':
                return SVGIcon;
            case 'jpg':
                return JPGIcon;
            case 'jpeg':
                return JPEGIcon;
            case 'png':
                return PNGIcon;
            case 'gif':
                return IMGIcon;
            case 'pdf':
                return PDFIcon;
            default:
                return PDFIcon;
        }
    };

    const baseClasses = ['border border-secondary rounded-xl p-3 bg-primary cursor-pointer w-full'];

    const formatFileSize = (bytes: number): string => {
        if (bytes === 0) return '0 B';
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return `${parseFloat((bytes / Math.pow(1024, i)).toFixed(2))} ${sizes[i]}`;
    };

    const getFileProgress = (file: File) => {
        const fileId = fileIds.value.get(file);
        if (fileId && fileProgresses.value.has(fileId)) {
            return fileProgresses.value.get(fileId)!;
        }
        return { progress: 0, currentSize: 0, totalSize: file.size };
    };
</script>

<template>
    <div class="w-full">
        <div
            :class="baseClasses"
            @click="handleClick"
            @dragenter="handleDragEnter"
            @dragleave="handleDragLeave"
            @dragover="handleDragOver"
            @drop="handleDrop"
        >
            <input
                ref="fileInput"
                type="file"
                class="hidden"
                :accept="accept"
                :multiple="multiple"
                @change="handleFileSelect"
            />

            <div class="flex flex-col items-center justify-center">
                <div class="mb-3 p-2.5 rounded-lg bg-primary border border-primary shadow-xs">
                    <UploadCloudIcon class="size-5 text-fg-secondary" />
                </div>

                <div class="text-center">
                    <p class="text-sm font-semibold text-brand-secondary">
                        {{ label }} <span class="font-normal text-tertiary">{{ dragText }}</span>
                    </p>
                    <p class="mt-1 text-sm text-tertiary">{{ formatText }} {{ maxSizeText }}</p>
                </div>
            </div>
        </div>

        <div v-if="selectedFile && !multiple" class="mt-3 rounded-xl border border-secondary bg-primary p-3 flex gap-3">
            <component :is="getIconForFile(selectedFile)" />
            <div class="flex-grow">
                <p class="mb-0.5 text-secondary font-medium text-sm">{{ selectedFile.name }}</p>
                <div class="flex items-center gap-2 h-3">
                    <p class="text-tertiary text-sm">
                        {{ formatFileSize(getFileProgress(selectedFile).currentSize) }} of
                        {{ formatFileSize(getFileProgress(selectedFile).totalSize) }}
                    </p>
                    <UDivider orientation="vertical" class="h-full" />
                    <div class="flex items-center gap-1">
                        <CheckCircleIcon
                            v-if="getFileProgress(selectedFile).progress === 100"
                            class="size-4 text-fg-success-primary"
                        />
                        <InfoCircleIcon
                            v-else-if="getFileProgress(selectedFile).progress === 0 && !_props.uploadUrl"
                            class="size-4 text-fg-warning-primary"
                        />
                        <UploadCloudIcon v-else class="size-4 text-fg-quaternary" />
                        <p
                            class="text-sm font-medium"
                            :class="{
                                'text-fg-success-primary': getFileProgress(selectedFile).progress === 100,
                                'text-fg-warning-primary':
                                    getFileProgress(selectedFile).progress === 0 && !_props.uploadUrl,
                                'text-quaternary':
                                    getFileProgress(selectedFile).progress > 0 &&
                                    getFileProgress(selectedFile).progress < 100,
                            }"
                        >
                            <template v-if="getFileProgress(selectedFile).progress === 100"> Complete </template>
                            <template v-else-if="getFileProgress(selectedFile).progress === 0 && !_props.uploadUrl">
                                Pending
                            </template>
                            <template v-else> Uploading... </template>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <UProgress :value="getFileProgress(selectedFile).progress" />
                    <p class="text-secondary">{{ getFileProgress(selectedFile).progress }}%</p>
                </div>
            </div>
        </div>

        <template v-if="multiple && selectedFiles.length > 0">
            <div
                v-for="file in selectedFiles"
                :key="file.name + file.size"
                class="mt-3 rounded-xl border border-secondary bg-primary p-3 flex gap-3"
            >
                <component :is="getIconForFile(file)" />
                <div class="flex-grow">
                    <p class="mb-0.5 text-secondary font-medium text-sm">{{ file.name }}</p>
                    <div class="flex items-center gap-2 h-3">
                        <p class="text-tertiary text-sm">
                            {{ formatFileSize(getFileProgress(file).currentSize) }} of
                            {{ formatFileSize(getFileProgress(file).totalSize) }}
                        </p>
                        <UDivider orientation="vertical" class="h-full" />
                        <div class="flex items-center gap-1">
                            <CheckCircleIcon
                                v-if="getFileProgress(file).progress === 100"
                                class="size-4 text-fg-success-primary"
                            />
                            <InfoCircleIcon
                                v-else-if="getFileProgress(file).progress === 0 && !_props.uploadUrl"
                                class="size-4 text-fg-warning-primary"
                            />
                            <UploadCloudIcon v-else class="size-4 text-fg-quaternary" />
                            <p
                                class="text-sm font-medium"
                                :class="{
                                    'text-fg-success-primary': getFileProgress(file).progress === 100,
                                    'text-fg-warning-primary':
                                        getFileProgress(file).progress === 0 && !_props.uploadUrl,
                                    'text-quaternary':
                                        getFileProgress(file).progress > 0 && getFileProgress(file).progress < 100,
                                }"
                            >
                                <template v-if="getFileProgress(file).progress === 100"> Complete </template>
                                <template v-else-if="getFileProgress(file).progress === 0 && !_props.uploadUrl">
                                    Pending
                                </template>
                                <template v-else> Uploading... </template>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <UProgress :value="getFileProgress(file).progress" />
                        <p class="text-secondary">{{ getFileProgress(file).progress }}%</p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
