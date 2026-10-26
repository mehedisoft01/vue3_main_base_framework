<script setup>
    import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
    import { useBase, useHttp, appStore } from '@/lib';
    import { useI18n } from 'vue-i18n';

    const { t, locale } = useI18n();
    const emit = defineEmits(['toggle-sidebar']);

    const { _l, getImage, httpReq, useGetters, urlGenerate, submitForm, assignStore } = { ...useBase(), ...useHttp(), ...appStore() };
    const { localization, authUser, appNotifications, appConfigs } = useGetters('localization', 'authUser', 'appNotifications', 'appConfigs');

    // State
    const searchQuery = ref('');
    const showSearchResults = ref(false);
    const activeTab = ref('all');
    const notificationOpen = ref(false);
    const cartItems = ref([
        { id: 1, name: 'Branded T-Shirts', image: 'img-1.png', quantity: 10, price: 32, total: 320 },
        { id: 2, name: 'Bentwood Chair', image: 'img-2.png', quantity: 5, price: 18, total: 89 },
        { id: 3, name: 'Borosil Paper Cup', image: 'img-3.png', quantity: 3, price: 250, total: 750 },
    ]);
    let intervalId = null;
    let dfLocale = ref(window.locale || 'en');

    const switchLang = (lang) => {
        submitForm({
            data : {request:'locale',locale:lang},
            url : `api/profile`,
            validation : false,
            callback : function(retData){
                locale.value = lang;
                dfLocale.value = lang;
            }
        });
    };
    const unreadNotifications = computed(() => appNotifications.value?.total || 0);
    const notifications = computed(() => appNotifications.value?.data || []);

    const filteredNotifications = computed(() => {
        if (activeTab.value === 'all') return notifications.value;
        return notifications.value.filter(n => n.type === activeTab.value);
    });

    const toggleSidebar = () => emit('toggle-sidebar');

    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    };

    const toggleTheme = () => {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', newTheme);
        document.body.classList.toggle('dark', newTheme === 'dark');
        localStorage.setItem('colorScheme', newTheme);
    };

    const handleSearch = () => {
        console.log('Searching for:', searchQuery.value);
    };

    const clearSearch = () => {
        searchQuery.value = '';
    };

    const hideSearchResults = () => {
        setTimeout(() => {
            if (!searchQuery.value) {
                showSearchResults.value = false;
            }
        }, 200);
    };

    // Notifications
    const loadNotifications = async (page = null) => {
        const notificationData = appNotifications.value;
        const params = {
            limit: notificationData?.limit || 10,
            page: (page ?? notificationData?.page) || 1
        };
        try {
            const data = await httpReq({
                method: 'get',
                url: urlGenerate('api/app_notification'),
                params
            });
            if (data) {
                assignStore('appNotifications', data);
            }
        } catch (err) {
            console.error('Failed to load notifications:', err);
        }
    };

    const startNotificationLoop = () => {
        stopNotificationLoop();
        const loop = async () => {
            let notifyPerMinute = parseInt(appConfigs.value?.notify_per_minuit) || 6;
            let intervalMs = Math.floor(60000 / notifyPerMinute);
            await loadNotifications();
            intervalId = setTimeout(loop, intervalMs);
        };
        loop();
    };

    const stopNotificationLoop = () => {
        if (intervalId) {
            clearTimeout(intervalId);
            intervalId = null;
        }
    };

    const openNotification = () => {
        notificationOpen.value = !notificationOpen.value;
        if (notificationOpen.value) {
            if (appNotifications.value) {
                appNotifications.value.page = 1;
            }
            startNotificationLoop();
        }
    };

    const markNotificationRead = (id) => {
        httpReq({
            method: 'get',
            url: `${urlGenerate('api/app_notification')}/${id}`
        });
    };

    const loadMoreNotifications = () => {
        loadNotifications(parseInt(appNotifications.value?.page || 1) + 1);
    };

    // Lifecycle
    onMounted(() => {
        loadNotifications();
        const notifyPerMinute = parseInt(appConfigs.value?.notify_per_minuit) || 6;
        const intervalMs = Math.floor(60000 / notifyPerMinute);
        if (intervalMs > 0) startNotificationLoop();
    });

    onBeforeUnmount(() => {
        stopNotificationLoop();
    });
</script>

<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <router-link to="/dashboard" class="logo logo-dark">
                            <span class="logo-sm">
                                <img :src="appConfigs?.app_logo" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img :src="appConfigs?.app_logo" alt="" height="17">
                            </span>
                        </router-link>
                        <router-link to="/dashboard" class="logo logo-light">
                            <span class="logo-sm">
                                <img :src="getImage('backend/images/logo-sm.png')" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img :src="getImage('backend/images/logo-light.png')" alt="" height="17">
                            </span>
                        </router-link>
                    </div>

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" @click="toggleSidebar">
                        <span class="hamburger-icon">
                            <span></span><span></span><span></span>
                        </span>
                    </button>

                    <!-- App Name -->
                    <div class="app-name d-none d-md-flex align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            {{ appConfigs?.app_name || 'Your App Name' }}
                        </h5>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <!-- Mobile Search -->
                    <div class="dropdown d-md-none topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown">
                            <i class="bx bx-search fs-22"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                            <form class="p-3" @submit.prevent="handleSearch">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." v-model="searchQuery">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <li class="nav-item dropdown dropdown-language d-none d-sm-flex">

                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret d-flex align-items-center"
                           href="#"
                           data-bs-toggle="dropdown">
                            <img :src="getImage('backend/images/flags/' + localization.find(l => l.locale === dfLocale)?.flag)"
                                 class="rounded me-2" height="18" alt="">
                            <span class="fw-medium">{{ localization.find(l => l.locale === dfLocale)?.name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                            <li v-for="locale in localization" :key="locale.locale" @click="switchLang(locale.locale)">
                                <a href="javascript:void(0)" class="dropdown-item d-flex align-items-center py-2">
                                    <img :src="getImage('backend/images/flags/' + locale.flag)" class="rounded me-2" height="18" alt="">
                                    <span>{{ locale.name }}</span>
                                    <i v-if="locale.locale === dfLocale" class="bx bx-check text-success ms-auto"></i>
                                </a>
                            </li>

                        </ul>

                    </li>

                    <!-- Fullscreen -->
                    <div class="ms-1 header-item d-none d-sm-flex">
                        <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" @click="toggleFullscreen">
                            <i class='bx bx-fullscreen fs-22'></i>
                        </button>
                    </div>

                    <!-- Dark/Light Mode -->
                    <div class="ms-1 header-item d-none d-sm-flex">
                        <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode" @click="toggleTheme">
                            <i class='bx bx-moon fs-22'></i>
                        </button>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                        <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" @click="openNotification">
                            <i class='bx bx-bell fs-22'></i>
                            <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{ unreadNotifications }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                            <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                                        </div>
                                        <div class="col-auto dropdown-tabs">
                                            <span class="badge bg-light text-body fs-13">{{ unreadNotifications }} New</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-2 pt-2">
                                    <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom">
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: activeTab === 'all' }" @click="activeTab = 'all'" href="#">All ({{ notifications.length }})</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: activeTab === 'messages' }" @click="activeTab = 'messages'" href="#">Messages</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: activeTab === 'alerts' }" @click="activeTab = 'alerts'" href="#">Alerts</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content position-relative">
                                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'all' }">
                                    <div data-simplebar style="max-height: 300px;" class="pe-2">
                                        <div class="text-reset notification-item d-block dropdown-item position-relative" v-for="notif in filteredNotifications" :key="notif.id">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0" v-if="notif.icon">
                                                    <span class="avatar-title" :class="'bg-' + notif.color + '-subtle text-' + notif.color + ' rounded-circle fs-16'">
                                                        <i :class="notif.icon"></i>
                                                    </span>
                                                </div>
                                                <img :src="getImage('backend/images/users/' + notif.avatar)" class="me-3 rounded-circle avatar-xs flex-shrink-0" alt="user-pic" v-else-if="notif.avatar">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link" @click="markNotificationRead(notif.id)">
                                                        <h6 class="mt-0 mb-2 lh-base" v-html="notif.title"></h6>
                                                        <div class="fs-13 text-muted" v-if="notif.message">
                                                            <p class="mb-1">{{ notif.message }}</p>
                                                        </div>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> {{ notif.time }}</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" :id="'notif-' + notif.id" v-model="notif.checked">
                                                        <label class="form-check-label" :for="'notif-' + notif.id"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-3 text-center view-all">
                                            <button type="button" class="btn btn-soft-success waves-effect waves-light" @click="loadMoreNotifications">View All Notifications <i class="ri-arrow-right-line align-middle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown">
                            <span class="d-flex align-items-center">
                                <img class="rounded-circle header-profile-user" :src="getImage(authUser.image, 'backend/images/avatars/avatar-26.png')" alt="Header Avatar">
                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ authUser?.name || 'Anna Adame' }}</span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">{{ _l('welcome') }} {{ authUser?.name || 'Anna!' }}</h6>
                            <router-link class="dropdown-item" to="/profile">
                                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">{{ _l('profile') }}</span>
                            </router-link>

                            <router-link class="dropdown-item" to="/settings">
                                <i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">{{ _l('settings') }}</span>
                            </router-link>
                            <a class="dropdown-item" :href="urlGenerate('logout')">
                                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle" data-key="t-logout">{{ _l('logout') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
<style scoped>
    .dropdown-language .nav-link{
        border:1px solid #dee2e6;
        border-radius:100px;
        padding:8px 8px;
        color:#495057;
        background:#fff;
        transition:.3s;
    }

    .dropdown-language .nav-link:hover{
        border-color:#405189;
        background:#f8f9fa;
    }

    .dropdown-language .dropdown-menu{
        min-width:180px;
        border-radius:10px;
    }

    .dropdown-language .dropdown-item:hover{
        background:#f3f6f9;
    }
</style>