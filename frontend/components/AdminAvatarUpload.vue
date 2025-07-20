<script setup lang="ts">
    import { ref, computed } from 'vue';

    interface Props {
        size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        currentAvatar?: string | null;
        userId: string;
        userType: 'admins' | 'clients' | 'owners';
        userName?: string;
    }

    interface Emits {
        (e: 'avatar-updated', avatarUrl: string): void;
        (e: 'avatar-deleted'): void;
    }

    const props = withDefaults(defineProps<Props>(), {
        size: 'xl',
        currentAvatar: null,
        userName: '',
    });

    const emit = defineEmits<Emits>();

    const isModalOpen = ref(false);

    const userInitials = computed(() => {
        if (!props.userName) return '';
        
        const names = props.userName.split(' ');
        if (names.length >= 2) {
            return `${names[0].charAt(0)}${names[1].charAt(0)}`.toUpperCase();
        }
        
        return names[0]?.charAt(0).toUpperCase() || '';
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

        <AdminAvatarUploadModal
            :is-open="isModalOpen"
            :current-avatar="currentAvatar"
            :user-id="userId"
            :user-type="userType"
            :user-name="userName"
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