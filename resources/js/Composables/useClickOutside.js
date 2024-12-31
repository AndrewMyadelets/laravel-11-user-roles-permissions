import { onMounted, onUnmounted } from 'vue';
const useClickOutside = (
    el,
    callback,
    excludeEl
) => {
    if (!el) {
        throw new Error('A target element must be provided.');
    }

    if (!callback) {
        throw new Error('A callback must be provided.');
    }

    const handleClickOutside = (event) => {
        if (
            event.target === el.value ||
            event.composedPath().includes(el.value) ||
            excludeEl && event.target === excludeEl.value ||
            excludeEl && event.composedPath().includes(excludeEl.value)
        ) {
            return;
        }

        if (typeof callback === 'function') {
            callback();
        }
    }

    onMounted(() => {
        window.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        window.removeEventListener('click', handleClickOutside);
    });
}

export default useClickOutside;
