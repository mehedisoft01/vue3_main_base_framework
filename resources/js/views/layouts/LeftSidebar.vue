<script setup>
    import { ref, onMounted, nextTick, watch, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import { useBase, useHttp, appStore } from '@/lib';

    const {_l, getImage, allMenus, loadConfigurations, useGetters} = {
        ...useBase(),
        ...useHttp(),
        ...appStore()
    };

    const {Config, appConfigs, authUser} = useGetters('Config', 'appConfigs', 'authUser');
    const route = useRoute();
    const props = defineProps({
        isVisible: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits([
        'close-sidebar'
    ]);

    const openMenus = ref([]);
    const searchKeyword = ref('');
    const filteredMenus = ref([]);

    const searchMenu = (keyword) => {
        if (!keyword || keyword.trim() === '') {
            filteredMenus.value = [];
            return;
        }

        const searchTerm = keyword.toLowerCase().trim();
        const results = [];

        const searchRecursive = (items, parentPath = []) => {
            for (const item of items) {
                const currentPath = [
                    ...parentPath,
                    item
                ];
                const fullPath = currentPath
                    .map(i => i.name)
                    .join(' > ');
                let matches = false;
                if (
                    item.name &&
                    item.name.toLowerCase().includes(searchTerm)
                ) {
                    matches = true;
                }
                let childResults = [];
                if (
                    item.submenus &&
                    item.submenus.length > 0
                ) {
                    childResults = searchRecursive(
                        item.submenus,
                        currentPath
                    );
                    if (childResults.length > 0) {
                        matches = true;
                    }
                }
                if (matches) {
                    const resultItem = {
                        ...item,
                        submenus: childResults.length > 0 ? childResults : item.submenus,
                        _matched: true,
                        _path: fullPath
                    };
                    results.push(resultItem);
                }
            }
            return results;
        };

        const allMenuItems = allMenus.value && allMenus.value.length > 0 ? allMenus.value : [];
        results.push(...searchRecursive(allMenuItems));
        filteredMenus.value = results;
    };

    watch(
        searchKeyword,
        (newVal) => {
            searchMenu(newVal);
        }
    );

    const displayMenus = computed(() => {
        if (
            searchKeyword.value &&
            searchKeyword.value.trim() !== '' &&
            filteredMenus.value.length > 0
        ) {
            return filteredMenus.value;
        }
        return allMenus.value && allMenus.value.length > 0 ? allMenus.value : [];
    });

    const toggleMenu = (menuId) => {
        const index = openMenus.value.indexOf(menuId);
        const isTopLevel = displayMenus.value.some(menu => menu.id === menuId);
        if (isTopLevel) {
            if (index > -1) {
                openMenus.value = [];
            } else {
                openMenus.value = [menuId];
            }
        } else {
            if (index > -1) {
                openMenus.value.splice(index, 1);
            } else {
                openMenus.value.push(menuId);
            }
        }
    };
    const isMenuOpen = (menuId) => {
        return openMenus.value.includes(menuId);
    };

    const icon = (iconClass) => {
        return iconClass || 'ri-dashboard-2-line';
    };

    const hasChildren = (menu) => {
        return (
            menu.submenus &&
            menu.submenus.length > 0
        );
    };

    const setActiveMenu = () => {
        const currentPath = route.path;
        document
            .querySelectorAll('#navbar-nav .nav-link')
            .forEach(link => {
                const href = link.getAttribute('href');
                const to = link.getAttribute('to');
                const linkPath = href || to;
                if (
                    linkPath &&
                    linkPath !== '#' &&
                    currentPath.includes(linkPath)
                ) {
                    let parent = link.closest('li');
                    while (parent) {
                        parent.classList.add('mm-active');
                        const parentUl = parent.querySelector('ul');
                        if (parentUl) {
                            parentUl.classList.add('show');
                            if (
                                parentUl.id &&
                                !openMenus.value.includes(parentUl.id)
                            ) {
                                openMenus.value = [parentUl.id];
                            }
                        }
                        parent = parent.parentElement?.closest('li');
                    }
                }
            });
    };

    watch(
        () => route.path,
        () => {
            searchKeyword.value = '';
            filteredMenus.value = [];
            nextTick(() => {
                setTimeout(setActiveMenu, 100);
            });
            if (window.innerWidth <= 768) {
                emit('close-sidebar');
            }
        }
    );

    onMounted(() => {
        loadConfigurations({
            callback: async () => {
                await nextTick();
                setTimeout(setActiveMenu, 100);
            }
        });
    });
</script>

<template>
    <div class="app-menu navbar-menu" :class="{ show: isVisible }">
        <div class="navbar-brand-box">
            <router-link to="/dashboard" class="logo logo-light">
                <span class="logo-sm">
                    <img :src="appConfigs?.app_logo || getImage('images/cm-logo.png')" alt="" height="40">
                </span>
                <span class="logo-lg">
                    <img :src="appConfigs?.app_logo || getImage('images/cm-logo.png')" alt="" height="40">
                </span>
            </router-link>
            <button type="button" class="mobile-sidebar-close" @click="emit('close-sidebar')" aria-label="Close sidebar">
                <i class="bx bx-arrow-back"></i>
            </button>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover desktop-sidebar-button" id="vertical-hover"></button>
        </div>
        <div id="scrollbar">
            <div class="container-fluid">
                <div id="two-column-menu"></div>
                <ul class="navbar-nav" id="navbar-nav">
                    <div class="search-box px-3 py-2 desktop-search">
                        <div class="input-group position-relative">
                            <span class="input-group-text bg-transparent border-0 position-absolute top-50 start-0 translate-middle-y z-1 ps-3">
                                <i class="ri-search-line text-muted"></i>
                            </span>
                            <input type="text" class="form-control border rounded-pill ps-5" :placeholder="_l('Search menu...')" v-model="searchKeyword" @input="searchMenu(searchKeyword)" style="height: 38px; background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.1); color: #fff;">
                            <span v-if="searchKeyword" class="position-absolute top-50 end-0 translate-middle-y pe-3 z-1" style="cursor: pointer;" @click="searchKeyword = ''">
                                <i class="ri-close-line text-muted"></i>
                            </span>
                        </div>
                        <div class="search-result-count mt-1 ms-2" v-if="searchKeyword && searchKeyword.trim() !== ''">
                            <small class="text-muted-50">{{ displayMenus.length }} {{ _l('results found') }}</small>
                        </div>
                    </div>
                    <li class="nav-item no-results" v-if="searchKeyword && searchKeyword.trim() !== '' && displayMenus.length === 0">
                        <div class="text-center py-4">
                            <i class="ri-search-line fs-2 text-muted d-block mb-2"></i>
                            <span class="text-muted">
                                {{ _l('No menu items found') }}
                            </span>
                        </div>
                    </li>
                    <template v-for="(menu, index) in displayMenus" :key="index">
                        <li class="nav-item" v-if="hasChildren(menu)">
                            <a  class="nav-link menu-link sticky-menu-parent" :href="'#' + menu.id" data-bs-toggle="collapse" role="button" :aria-expanded="isMenuOpen(menu.id) ? 'true' : 'false'" :aria-controls="menu.id" @click.prevent="toggleMenu(menu.id)">
                                <i :class="icon(menu.icon)"></i>
                                <span :data-key="menu.key || 't-' + menu.name.toLowerCase()">
                                    {{ _l(menu.name) }}
                                </span>
                                <span v-if="menu._matched" class="badge bg-primary ms-auto">matched</span>
                            </a>
                            <div class="collapse menu-dropdown" :id="menu.id" :class="{ show: isMenuOpen(menu.id) }">
                                <ul class="nav nav-sm flex-column">
                                    <template v-for="(sub, subIndex) in menu.submenus" :key="subIndex">
                                        <li class="nav-item" v-if="hasChildren(sub)">
                                            <a class="nav-link" :href="'#' + sub.id" data-bs-toggle="collapse" role="button" :aria-expanded="isMenuOpen(sub.id) ? 'true' : 'false'" :aria-controls="sub.id" @click.prevent="toggleMenu(sub.id)">
<!--                                                <i :class="icon(sub.icon)"></i>-->
                                                <span :data-key="sub.key || 't-' + sub.name.toLowerCase()">
                                                    {{ _l(sub.name) }}
                                                </span>
                                            </a>
                                            <div class="collapse menu-dropdown" :id="sub.id" :class="{ show: isMenuOpen(sub.id) }">
                                                <ul class="nav nav-sm flex-column">
                                                    <template v-for="(subSub, subSubIndex) in sub.submenus" :key="subSubIndex">
                                                        <li class="nav-item" v-if="hasChildren(subSub)">
                                                            <a class="nav-link" :href="'#' + subSub.id" role="button" :aria-expanded="isMenuOpen(subSub.id) ? 'true' : 'false'" :aria-controls="subSub.id" @click.prevent="toggleMenu(subSub.id)">
                                                                <i :class="icon(subSub.icon)"></i>
                                                                <span :data-key="subSub.key || 't-' + subSub.name.toLowerCase()">
                                                                    {{ _l(subSub.name) }}
                                                                </span>
                                                            </a>
                                                            <div class="collapse menu-dropdown" :id="subSub.id" :class="{ show: isMenuOpen(subSub.id) }">
                                                                <ul class="nav nav-sm flex-column">
                                                                    <li class="nav-item" v-for="(subSubSub, subSubSubIndex) in subSub.submenus" :key="subSubSubIndex">
                                                                        <router-link :to="subSubSub.link || '#'" class="nav-link">
                                                                            <i :class="icon(subSubSub.icon)"></i>
                                                                            {{ _l(subSubSub.name) }}
                                                                        </router-link>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        <li class="nav-item" v-else>
                                                            <router-link :to="subSub.link || '#'" class="nav-link">
<!--                                                                <i :class="icon(subSub.icon)"></i>-->
                                                                {{ _l(subSub.name) }}
                                                            </router-link>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="nav-item" v-else>
                                            <router-link :to="sub.link || '#'" class="nav-link">
                                                {{ _l(sub.name) }}
                                            </router-link>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item" v-else>
                            <router-link class="nav-link menu-link" :to="menu.link || '#'">
                                <i :class="icon(menu.icon)"></i>
                                <span :data-key="menu.key || 't-' + menu.name.toLowerCase()">
                                    {{ _l(menu.name) }}
                                </span>
                            </router-link>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
        <div class="sidebar-background"></div>
    </div>
</template>

<style scoped>
    #scrollbar {
        height: calc(100vh - 70px);
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sticky-menu-parent {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    #navbar-nav {
        min-height: 100%;
    }
    .navbar-brand-box {
        position: relative;
    }
    .mobile-sidebar-close {
        display: none;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        padding: 0;
        border: 0;
        background: transparent;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        z-index: 20;
    }
    .desktop-sidebar-button {
        display: block;
    }
    @media (min-width: 992px) {
        .mobile-sidebar-close {
            display: none !important;
        }
        .desktop-sidebar-button {
            display: block;
        }
        .desktop-search {
            display: block;
        }
    }
    @media (max-width: 991.98px) {
        .navbar-brand-box {
            position: relative;
            display: flex;
            align-items: center;
            height: 70px;
            padding: 0 15px;
        }
        .navbar-brand-box .logo {
            display: block !important;
        }
        .desktop-sidebar-button {
            display: none !important;
        }
        .mobile-sidebar-close {
            display: flex !important;
        }
        .desktop-search {
            display: none !important;
        }

    }
    @media (max-width: 575.98px) {
        .navbar-brand-box {
            height: 65px;
            padding: 0 12px;
        }
        .mobile-sidebar-close {
            right: 10px;
            width: 36px;
            height: 36px;
            font-size: 22px;
        }
        #scrollbar {
            height: calc(100vh - 65px);
        }
    }
</style>
