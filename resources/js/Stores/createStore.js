import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const createStore = (storeName) => {
    return defineStore(storeName, () => {
        const currentPage = ref(1);

        const selectAll = ref(false);
        const selectedItems = ref([]);

        const filters = ref({
            search: '',
            sort: '',
            order: ''
        });

        const queryParams = computed(() => {
            const params = {};
            if (filters.value.search) params.search = filters.value.search;
            if (filters.value.sort) params.sort = filters.value.sort;
            if (filters.value.order) params.order = filters.value.order;
            if (currentPage.value && currentPage.value > 1) params.page = currentPage.value;

            return params;
        });

        return {
            currentPage,
            selectAll,
            selectedItems,
            filters,
            queryParams
        };
    });
};
