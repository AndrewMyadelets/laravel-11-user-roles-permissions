<script setup>
import Sidebar from "@/Components/Admin/Sidebar.vue";
import Header from "@/Components/Admin/Header.vue";

</script>

<template>
    <div class="flex h-full">
        <Sidebar/>
        <div class="flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <Header/>
            <main class="flex-1 bg-gray-100">
                <div class="p-8 h-full w-full">
                    <slot></slot>
                </div>
            </main>
        </div>
    </div>
</template>
