<script setup lang="ts">
    import { ref, onMounted, onUnmounted, nextTick } from 'vue';

    interface TooltipProps {
        text: string;
        delayDuration?: number;
        position?: 'top' | 'right' | 'bottom' | 'left';
        maxWidth?: string;
    }

    const props = withDefaults(defineProps<TooltipProps>(), {
        delayDuration: 700,
        position: 'top',
        maxWidth: '200px',
    });

    const tooltipVisible = ref(false);
    const tooltipElement = ref<HTMLElement | null>(null);
    const triggerElement = ref<HTMLElement | null>(null);
    const showTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

    function showTooltip() {
        if (showTimeout.value) clearTimeout(showTimeout.value);
        showTimeout.value = setTimeout(async () => {
            tooltipVisible.value = true;
            await nextTick();
            updatePosition();
        }, props.delayDuration);
    }

    function hideTooltip() {
        if (showTimeout.value) clearTimeout(showTimeout.value);
        tooltipVisible.value = false;
    }

    function updatePosition() {
        if (!tooltipElement.value || !triggerElement.value) return;

        const trigger = triggerElement.value.getBoundingClientRect();
        const tooltip = tooltipElement.value.getBoundingClientRect();
        
        // Using window.scrollX and window.scrollY for more consistent cross-browser behavior
        const scrollTop = window.scrollY;
        const scrollLeft = window.scrollX;

        let top = 0;
        let left = 0;
        const originalPosition = props.position;
        let actualPosition = props.position;

        switch (props.position) {
            case 'top':
                top = trigger.top + scrollTop - tooltip.height - 10;
                left = trigger.left + scrollLeft + (trigger.width / 2) - (tooltip.width / 2);
                break;
            case 'right':
                top = trigger.top + scrollTop + (trigger.height / 2) - (tooltip.height / 2);
                left = trigger.left + scrollLeft + trigger.width + 10;
                break;
            case 'bottom':
                top = trigger.top + scrollTop + trigger.height + 10;
                left = trigger.left + scrollLeft + (trigger.width / 2) - (tooltip.width / 2);
                break;
            case 'left':
                top = trigger.top + scrollTop + (trigger.height / 2) - (tooltip.height / 2);
                left = trigger.left + scrollLeft - tooltip.width - 10;
                break;
        }

        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        if (actualPosition === 'top' && top < scrollTop + 10) {
            actualPosition = 'bottom';
            top = trigger.top + scrollTop + trigger.height + 10;
        } else if (actualPosition === 'bottom' && top + tooltip.height > scrollTop + viewportHeight - 10) {
            actualPosition = 'top';
            top = trigger.top + scrollTop - tooltip.height - 10;
        } else if (actualPosition === 'left' && left < scrollLeft + 10) {
            actualPosition = 'right';
            left = trigger.left + scrollLeft + trigger.width + 10;
        } else if (actualPosition === 'right' && left + tooltip.width > scrollLeft + viewportWidth - 10) {
            actualPosition = 'left';
            left = trigger.left + scrollLeft - tooltip.width - 10;
        }

        if (left < scrollLeft + 10) left = scrollLeft + 10;
        if (left + tooltip.width > scrollLeft + viewportWidth - 10) 
            left = scrollLeft + viewportWidth - tooltip.width - 10;
        
        if (top < scrollTop + 10) top = scrollTop + 10;
        if (top + tooltip.height > scrollTop + viewportHeight - 10)
            top = scrollTop + viewportHeight - tooltip.height - 10;

        tooltipElement.value.style.top = `${Math.round(top)}px`;
        tooltipElement.value.style.left = `${Math.round(left)}px`;
        
        if (originalPosition !== actualPosition) {
            tooltipElement.value.dataset.position = actualPosition;
            
            const arrowElement = tooltipElement.value.querySelector('div');
            if (arrowElement) {
                arrowElement.classList.remove(
                    'bottom-[-4px]', 'left-1/2', '-ml-1',
                    'left-[-4px]', 'top-1/2', '-mt-1',
                    'top-[-4px]',
                    'right-[-4px]'
                );
                
                // Add the correct position classes based on actual position
                switch (actualPosition) {
                    case 'top':
                        arrowElement.classList.add('bottom-[-4px]', 'left-1/2', '-ml-1');
                        break;
                    case 'right':
                        arrowElement.classList.add('left-[-4px]', 'top-1/2', '-mt-1');
                        break;
                    case 'bottom':
                        arrowElement.classList.add('top-[-4px]', 'left-1/2', '-ml-1');
                        break;
                    case 'left':
                        arrowElement.classList.add('right-[-4px]', 'top-1/2', '-mt-1');
                        break;
                }
            }
        }
    }

    function handleEscapeKey(e: KeyboardEvent) {
        if (e.key === 'Escape') {
            hideTooltip();
        }
    }

    onMounted(() => {
        window.addEventListener('keydown', handleEscapeKey);
        window.addEventListener('resize', updatePosition);
        window.addEventListener('scroll', updatePosition, true); // Capture scroll events on any element
    });

    onUnmounted(() => {
        if (showTimeout.value) clearTimeout(showTimeout.value);
        window.removeEventListener('keydown', handleEscapeKey);
        window.removeEventListener('resize', updatePosition);
        window.removeEventListener('scroll', updatePosition, true);
    });
</script>

<template>
    <div
        ref="triggerElement"
        class="inline-block relative"
        @mouseenter="showTooltip"
        @mouseleave="hideTooltip"
        @focus="showTooltip"
        @blur="hideTooltip"
    >
        <slot />

        <Transition
            name="tooltip"
        >
            <div
                v-if="tooltipVisible"
                ref="tooltipElement"
                class="fixed z-50 py-2 px-3 bg-primary-solid text-white rounded-lg text-xs pointer-events-none shadow-lg text-center tooltip-container"
                :style="{ maxWidth: maxWidth }"
                role="tooltip"
                aria-live="polite"
            >
                {{ text }}
                <div
                    class="absolute w-2 h-2 bg-primary-solid rotate-45"
                    :class="{
                        'bottom-[-4px] left-1/2 -ml-1': position === 'top',
                        'left-[-4px] top-1/2 -mt-1': position === 'right',
                        'top-[-4px] left-1/2 -ml-1': position === 'bottom',
                        'right-[-4px] top-1/2 -mt-1': position === 'left',
                    }"
                ></div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.tooltip-container {
    transform-origin: center;
    will-change: transform, opacity;
}

.tooltip-enter-active {
    transition: all 0.2s ease-out;
}

.tooltip-leave-active {
    transition: all 0.15s ease-in;
}

.tooltip-enter-from,
.tooltip-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.tooltip-enter-to,
.tooltip-leave-from {
    opacity: 1;
    transform: scale(1);
}
</style>
