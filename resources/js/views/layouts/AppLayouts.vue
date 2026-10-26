<template>
    <div>
        <div class="flex justify-center items-center h-full">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-green-500"></div>
        </div>

        <div class="wrapper">

            <LeftSidebar :is-visible="isSidebarVisible" @close-sidebar="closeSidebar"/>
            <Header @toggle-sidebar="toggleSidebar"/>
            <div class="main-content">
                <router-view />
            </div>

            <div class="overlay toggle-icon" @click="closeSidebar"></div>
            <a href="#" class="back-to-top">
                <i class="bx bxs-up-arrow-alt"></i>
            </a>

            <appFooter />

        </div>

        <themeSwitcher />
    </div>
</template>

<script setup>
    import { ref, watch, onBeforeUnmount } from 'vue';

    import Header from "@/views/layouts/Header.vue";
    import LeftSidebar from "@/views/layouts/LeftSidebar.vue";
    import appFooter from "@/views/layouts/appFooter.vue";
    import themeSwitcher from "@/views/layouts/themeSwitcher.vue";

    const isSidebarVisible = ref(false);

    const toggleSidebar = () => {
        isSidebarVisible.value = !isSidebarVisible.value;

        if (window.innerWidth <= 991) {
            document.body.classList.toggle(
                'vertical-sidebar-enable',
                isSidebarVisible.value
            );
        }
    };

    const closeSidebar = () => {
        isSidebarVisible.value = false;

        document.body.classList.remove('vertical-sidebar-enable');
    };

    watch(isSidebarVisible, (value) => {
        if (window.innerWidth <= 991) {
            document.body.classList.toggle(
                'vertical-sidebar-enable',
                value
            );
        }
    });

    onBeforeUnmount(() => {
        document.body.classList.remove('vertical-sidebar-enable');
    });
</script>