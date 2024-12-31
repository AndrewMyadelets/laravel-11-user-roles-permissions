<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
import { computed, watch } from 'vue';

const props = defineProps({
    current: {
        type: Number,
        required: true
    },
    total: {
        type: Number,
        default: 0
    },
    perPage: {
        type: Number,
        default: 10
    },
    pageRange: {
        type: Number,
        default: 2
    }
});

const emits = defineEmits(['changePage']);

const rangeStart = computed(() => {
    const start = props.current - props.pageRange;

    return start > 0 ? start : 1;
});

const rangeEnd = computed(() => {
    const end = props.current + props.pageRange;

    return end < totalPages.value ? end : totalPages.value;
});

const totalPages = computed(() => {
    return Math.ceil(props.total / props.perPage);
});

const pages = computed(() => {
    const pages = [];
    for (let i = rangeStart.value; i <= rangeEnd.value; i++) {
        pages.push(i);
    }

    return pages;
});

const hasThreeDotsAfterFirst = computed(() => {
    return props.current - props.pageRange > 2;
});

const hasThreeDotsBeforeLast = computed(() => {
    return totalPages.value - props.current - props.pageRange >= 2;
});

const itemsFrom = computed(() => {
    return (props.current - 1) * props.perPage + 1;
});

const itemsTo = computed(() => {
    const itemsToPerPage = (props.current - 1) * props.perPage + props.perPage;
    return itemsToPerPage < props.total ? itemsToPerPage : props.total;
});

watch(totalPages, (newValue) => {
    if (props.current > newValue) {
        emits('changePage', newValue);
    }
});
</script>

<template>
    <div v-if="totalPages > 1" class="py-3 flex justify-center select-none">
        <div
            class="p-4 bg-white rounded-lg flex flex-col space-y-4 items-center flex-wrap sm:flex-1 sm:flex-row sm:justify-between sm:items-center sm:space-y-0"
        >
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ itemsFrom }}</span> to
                    <span class="font-medium">{{ itemsTo }}</span> of
                    <span class="font-medium">{{ total }}</span>
                </p>
            </div>
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <div
                        v-if="current > 1"
                        @click="emits('changePage', current - 1)"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 cursor-pointer"
                    >
                        <span class="sr-only">Previous</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </div>
                    <div
                        v-if="!pages.includes(1)"
                        @click="emits('changePage', 1)"
                        :class="
                            current === 1
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-900 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 cursor-pointer'
                        "
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300"
                    >
                        1
                    </div>
                    <span
                        v-if="hasThreeDotsAfterFirst"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
                        >...
                    </span>
                    <template v-for="page in pages">
                        <div
                            @click="emits('changePage', page)"
                            :class="
                                current === page
                                    ? 'bg-indigo-600 text-white'
                                    : 'text-gray-900 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 cursor-pointer'
                            "
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300"
                        >
                            {{ page }}
                        </div>
                    </template>
                    <span
                        v-if="hasThreeDotsBeforeLast"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
                        >...
                    </span>
                    <div
                        v-if="!pages.includes(totalPages)"
                        @click="emits('changePage', totalPages)"
                        :class="
                            current === totalPages
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-900 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 cursor-pointer'
                        "
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300"
                    >
                        {{ totalPages }}
                    </div>
                    <div
                        v-if="current < totalPages"
                        @click="emits('changePage', current + 1)"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 cursor-pointer"
                    >
                        <span class="sr-only">Next</span>
                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                    </div>
                </nav>
            </div>
        </div>
    </div>
</template>
