import { ref } from 'vue';

const sidebarOpen = ref(false);
const toggleSidebar = ref(null);
const sidebar = ref(null);

export const useSidebar = () => {
    return {
        sidebarOpen,
        toggleSidebar,
        sidebar
    }
}
