<script setup>
import { BarsArrowUpIcon, BarsArrowDownIcon } from '@heroicons/vue/24/outline';

defineProps({
  field: String,
  sort: String,
  order: String,
});

const emit = defineEmits(['click']);

</script>

<template>
  <th
      class="p-2 cursor-pointer"
      @click="emit('click')"
      :class="sort === field ? 'bg-gray-400' : ''"
  >
    <div class="flex justify-between">
      <slot>{{ field }}</slot>
      <div
          v-if="sort === field"
          class="ml-2"
      >
        <BarsArrowUpIcon
            v-if="order === 'asc'"
            class="w-5 h-5"
        />
        <BarsArrowDownIcon
            v-else
            class="w-5 h-5"
        />
      </div>
    </div>
  </th>
</template>
