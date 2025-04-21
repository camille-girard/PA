<script setup lang="ts">
    interface AvatarProps {
        size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        statusIcon?: 'false' | 'online' | 'company' | 'verified';
        imageSrc?: string;
        text?: string;
    }

    const _props = withDefaults(defineProps<AvatarProps>(), {
        size: 'xs',
        imageSrc: '',
        statusIcon: 'false',
        text: '',
    });

    const baseClasses =
        'relative z-0 rounded-full flex items-center justify-center object-cover overflow-hidden bg-tertiary border-avatar box-border';

    const sizeClasses = {
        xs: 'size-6',
        sm: 'size-8',
        md: 'size-10',
        lg: 'size-12',
        xl: 'size-14',
        '2xl': 'size-16',
    };

    const placeholderSizeClasses = {
        xs: 'size-4',
        sm: 'size-5',
        md: 'size-6',
        lg: 'size-7',
        xl: 'size-8',
        '2xl': 'size-8',
    };

    const textSizeClasses = {
        xs: 'text-xs',
        sm: 'text-sm',
        md: 'text-md',
        lg: 'text-lg',
        xl: 'text-xl',
        '2xl': 'text-2xl',
    };
</script>

<template>
    <div class="relative h-fit w-fit">
        <div :class="[baseClasses, sizeClasses[size]]">
            <img v-if="imageSrc" :src="imageSrc" />
            <p v-else-if="text" :class="[textSizeClasses[size], 'text-quaternary font-semibold']">
                {{ text }}
            </p>
            <UserIcon v-else :class="['text-utility-gray-400', placeholderSizeClasses[size]]" />
        </div>
        <VerifiedTickIcon v-if="statusIcon === 'verified'" :size="size" class="absolute bottom-0 right-0 z-10" />
    </div>
</template>
