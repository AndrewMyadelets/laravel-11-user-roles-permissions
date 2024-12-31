<script setup>
import {Head} from "@inertiajs/vue3";

defineProps({
    title: String
});
</script>

<template>
    <Head :title="title"/>
    <h1 class="text-2xl">{{ title }}</h1>
</template>
