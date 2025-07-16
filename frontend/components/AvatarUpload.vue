<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { useAuthStore } from '~/stores/auth';

    interface Props {
        size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        currentAvatar?: string | null;
    }

    interface Emits {
        (e: 'avatar-updated', avatarUrl: string): void;
        (e: 'avatar-deleted'): void;
    }

    withDefaults(defineProps<Props>(), {
        size: 'xl',
        currentAvatar: null,
    });

    const emit = defineEmits<Emits>();

    const authStore = useAuthStore();
    const isModalOpen = ref(false);

    const userInitials = computed(() => {
        if (!authStore.user) return '';

        const { firstName, lastName } = authStore.user;

        if (firstName && lastName) {
            return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
        }

        if (firstName) {
            return firstName.charAt(0).toUpperCase();
        }

        return '';
    });

    const openModal = () => {
        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
    };

    const handleAvatarUpdated = (avatarUrl: string) => {
        emit('avatar-updated', avatarUrl);
    };

    const handleAvatarDeleted = () => {
        emit('avatar-deleted');
    };
</script>

<template>
    <div class="avatar-upload">
        <div class="relative inline-block cursor-pointer group" @click="openModal">
            <UAvatar :image-src="currentAvatar" :size="size" :text="userInitials" />

            <div
                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 rounded-full transition-all duration-200 flex items-center justify-center"
            >
                <EditIcon
                    class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                />
            </div>
        </div>

        <AvatarUploadModal
            :is-open="isModalOpen"
            :current-avatar="currentAvatar"
            @close="closeModal"
            @avatar-updated="handleAvatarUpdated"
            @avatar-deleted="handleAvatarDeleted"
        />
    </div>
</template>

<style scoped>
    .avatar-upload {
        @apply inline-block;
    }
</style>
