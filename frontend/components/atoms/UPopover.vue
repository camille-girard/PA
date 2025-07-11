<script setup lang="ts">
    interface PopoverProps {
        trigger?: 'click' | 'hover';
        contentAlign?: 'start' | 'center' | 'end';
        contentSide?: 'top' | 'right' | 'bottom' | 'left';
        contentSideOffset?: number;
        closeOnClickOutside?: boolean;
        modelValue?: boolean;
        teleport?: boolean;
        teleportTo?: string;
    }

    const props = withDefaults(defineProps<PopoverProps>(), {
        trigger: 'click',
        contentAlign: 'center',
        contentSide: 'bottom',
        contentSideOffset: 8,
        closeOnClickOutside: true,
        modelValue: false,
        teleport: false,
        teleportTo: 'body',
    });

    const emit = defineEmits<{
        'update:modelValue': [value: boolean];
    }>();

    const isOpenLocal = ref(props.modelValue);
    const triggerRef = ref<HTMLElement | null>(null);
    const contentRef = ref<HTMLElement | null>(null);
    const triggerPosition = ref({ top: 0, left: 0, width: 0, height: 0 });

    watch(
        () => props.modelValue,
        (newValue) => {
            isOpenLocal.value = newValue;
        }
    );

    const isOpen = computed({
        get: () => isOpenLocal.value,
        set: (value) => {
            isOpenLocal.value = value;
            emit('update:modelValue', value);
        },
    });

    const positionStyles = computed(() => {
        if (!props.teleport) {
            const styles: Record<string, string> = {};

            switch (props.contentSide) {
                case 'top':
                    styles.bottom = '100%';
                    styles.marginBottom = `${props.contentSideOffset}px`;
                    break;
                case 'right':
                    styles.left = '100%';
                    styles.marginLeft = `${props.contentSideOffset}px`;
                    break;
                case 'bottom':
                    styles.top = '100%';
                    styles.marginTop = `${props.contentSideOffset}px`;
                    break;
                case 'left':
                    styles.right = '100%';
                    styles.marginRight = `${props.contentSideOffset}px`;
                    break;
            }

            switch (props.contentAlign) {
                case 'start':
                    if (['top', 'bottom'].includes(props.contentSide)) styles.left = '0';
                    else styles.top = '0';
                    break;
                case 'center':
                    if (['top', 'bottom'].includes(props.contentSide)) {
                        styles.left = '50%';
                        styles.transform = 'translateX(-50%)';
                    } else {
                        styles.top = '50%';
                        styles.transform = 'translateY(-50%)';
                    }
                    break;
                case 'end':
                    if (['top', 'bottom'].includes(props.contentSide)) styles.right = '0';
                    else styles.bottom = '0';
                    break;
            }

            return styles;
        } else {
            const styles: Record<string, string> = {
                position: 'absolute',
                zIndex: '50',
            };

            let top = triggerPosition.value.top;
            let left = triggerPosition.value.left;

            switch (props.contentSide) {
                case 'top':
                    top = triggerPosition.value.top - props.contentSideOffset;
                    break;
                case 'right':
                    left = triggerPosition.value.left + triggerPosition.value.width + props.contentSideOffset;
                    break;
                case 'bottom':
                    top = triggerPosition.value.top + triggerPosition.value.height + props.contentSideOffset;
                    break;
                case 'left':
                    left = triggerPosition.value.left - props.contentSideOffset;
                    break;
            }

            switch (props.contentAlign) {
                case 'start':
                    if (['top', 'bottom'].includes(props.contentSide)) {
                        left = triggerPosition.value.left;
                    } else {
                        top = triggerPosition.value.top;
                    }
                    break;
                case 'center':
                    if (['top', 'bottom'].includes(props.contentSide)) {
                        left = triggerPosition.value.left + triggerPosition.value.width / 2;
                        styles.transform = 'translateX(-50%)';
                    } else {
                        top = triggerPosition.value.top + triggerPosition.value.height / 2;
                        styles.transform = 'translateY(-50%)';
                    }
                    break;
                case 'end':
                    if (['top', 'bottom'].includes(props.contentSide)) {
                        left = triggerPosition.value.left + triggerPosition.value.width;
                        styles.transform = 'translateX(-100%)';
                    } else {
                        top = triggerPosition.value.top + triggerPosition.value.height;
                        styles.transform = 'translateY(-100%)';
                    }
                    break;
            }

            if (props.contentSide === 'top' && props.contentAlign === 'center') {
                styles.transform = 'translate(-50%, -100%)';
            } else if (props.contentSide === 'top' && props.contentAlign === 'end') {
                styles.transform = 'translateX(-100%) translateY(-100%)';
            } else if (props.contentSide === 'top' && props.contentAlign === 'start') {
                styles.transform = 'translateY(-100%)';
            } else if (props.contentSide === 'left' && props.contentAlign === 'center') {
                styles.transform = 'translate(-100%, -50%)';
            } else if (props.contentSide === 'left' && props.contentAlign === 'end') {
                styles.transform = 'translate(-100%, -100%)';
            } else if (props.contentSide === 'left' && props.contentAlign === 'start') {
                styles.transform = 'translateX(-100%)';
            }

            styles.top = `${top}px`;
            styles.left = `${left}px`;

            return styles;
        }
    });

    const updateTriggerPosition = () => {
        if (props.teleport && triggerRef.value) {
            const rect = triggerRef.value.getBoundingClientRect();
            triggerPosition.value = {
                top: rect.top + window.scrollY,
                left: rect.left + window.scrollX,
                width: rect.width,
                height: rect.height,
            };
        }
    };

    const toggle = () => {
        if (props.teleport) {
            updateTriggerPosition();
        }
        isOpen.value = !isOpen.value;
    };

    const open = () => {
        if (props.teleport) {
            updateTriggerPosition();
        }
        isOpen.value = true;
    };

    const close = () => {
        isOpen.value = false;
    };

    const handleClickOutside = (event: MouseEvent) => {
        if (
            props.closeOnClickOutside &&
            isOpen.value &&
            contentRef.value &&
            triggerRef.value &&
            !contentRef.value.contains(event.target as Node) &&
            !triggerRef.value.contains(event.target as Node)
        ) {
            close();
        }
    };

    onMounted(() => {
        if (props.closeOnClickOutside) {
            document.addEventListener('click', handleClickOutside);
        }
        if (props.teleport) {
            window.addEventListener('scroll', updateTriggerPosition);
            window.addEventListener('resize', updateTriggerPosition);
        }
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleClickOutside);
        if (props.teleport) {
            window.removeEventListener('scroll', updateTriggerPosition);
            window.removeEventListener('resize', updateTriggerPosition);
        }
    });

    defineExpose({
        open,
        close,
        toggle,
        isOpen,
    });
</script>

<template>
    <div class="relative inline-block">
        <div
            ref="triggerRef"
            class="inline-flex"
            @click="props.trigger === 'click' && toggle()"
            @mouseenter="props.trigger === 'hover' && open()"
            @mouseleave="props.trigger === 'hover' && close()"
        >
            <slot></slot>
        </div>

        <Transition v-if="!teleport" name="popover">
            <div v-if="isOpen" ref="contentRef" class="absolute z-50 bg-transparent" :style="positionStyles">
                <slot name="content"></slot>
            </div>
        </Transition>

        <Teleport v-else :to="teleportTo">
            <Transition name="popover">
                <div v-if="isOpen" ref="contentRef" class="bg-transparent" :style="positionStyles">
                    <slot name="content"></slot>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
    .popover-enter-active {
        transition: all 0.2s ease-out;
    }

    .popover-leave-active {
        transition: all 0.15s ease-in;
    }

    .popover-enter-from,
    .popover-leave-to {
        transform: scale(0.95);
        opacity: 0;
    }

    .popover-enter-to,
    .popover-leave-from {
        transform: scale(1);
        opacity: 1;
    }
</style>
