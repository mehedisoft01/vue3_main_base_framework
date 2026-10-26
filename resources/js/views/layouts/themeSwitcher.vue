<template>
    <div class="customizer-setting d-none d-md-block">
        <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" @click="togglePanel">
            <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
        </div>
    </div>

    <div class="offcanvas offcanvas-end border-0" :class="{ show: activePanel }" tabindex="-1">
        <div class="d-flex align-items-center bg-primary bg-gradient p-3 offcanvas-header">
            <h5 class="m-0 me-2 text-white">Theme Customizer</h5>
            <button type="button" class="btn-close btn-close-white ms-auto" @click="togglePanel"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div data-simplebar class="h-100">
                <div class="p-4">
                    <!-- Layout -->
                    <h6 class="mb-0 fw-semibold text-uppercase">Layout</h6>
                    <p class="text-muted">Choose your layout</p>
                    <div class="row gy-3">
                        <div class="col-4" v-for="layout in layouts" :key="layout.value">
                            <div class="form-check card-radio">
                                <input :id="'customizer-layout' + layout.value" name="data-layout" type="radio" :value="layout.value" class="form-check-input" v-model="selectedLayout">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'customizer-layout' + layout.value">
                                    <span class="d-flex gap-1 h-100" v-html="layout.preview"></span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">{{ layout.name }}</h5>
                        </div>
                    </div>

                    <!-- Sidebar User Profile Switch -->
                    <div class="form-check form-switch form-switch-md mb-3 mt-4">
                        <input type="checkbox" class="form-check-input" id="sidebarUserProfile" v-model="showUserProfile">
                        <label class="form-check-label" for="sidebarUserProfile">Sidebar User Profile Avatar</label>
                    </div>

                    <!-- Theme -->
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Theme</h6>
                    <p class="text-muted">Choose your suitable Theme.</p>
                    <div class="row">
                        <div class="col-6" v-for="theme in themes" :key="theme.value">
                            <div class="form-check card-radio">
                                <input :id="'customizer-theme' + theme.value" name="data-theme" type="radio" :value="theme.value" class="form-check-input" v-model="selectedTheme">
                                <label class="form-check-label p-0" :for="'customizer-theme' + theme.value">
                                    <img :src="getImage('backend/images/demo/' + theme.image)" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">{{ theme.name }}</h5>
                        </div>
                    </div>

                    <!-- Color Scheme -->
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Color Scheme</h6>
                    <p class="text-muted">Choose Light or Dark Scheme.</p>
                    <div class="colorscheme-cardradio">
                        <div class="row">
                            <div class="col-4" v-for="scheme in colorSchemes" :key="scheme.value">
                                <div class="form-check card-radio" :class="{ dark: scheme.value === 'dark' }">
                                    <input class="form-check-input" type="radio" name="data-bs-theme" :id="'layout-mode-' + scheme.value" :value="scheme.value" v-model="selectedColorScheme">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" :class="scheme.value === 'dark' ? 'bg-dark' : ''" :for="'layout-mode-' + scheme.value">
                                        <span class="d-flex gap-1 h-100" v-html="scheme.preview"></span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">{{ scheme.name }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Visibility -->
                    <div id="sidebar-visibility">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Visibility</h6>
                        <p class="text-muted">Choose show or Hidden sidebar.</p>
                        <div class="row">
                            <div class="col-4" v-for="option in visibilityOptions" :key="option.value">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-visibility" :id="'sidebar-visibility-' + option.value" :value="option.value" v-model="selectedVisibility">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'sidebar-visibility-' + option.value">
                                        <span class="d-flex gap-1 h-100" v-html="option.preview"></span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">{{ option.name }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Width -->
                    <div id="layout-width">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Layout Width</h6>
                        <p class="text-muted">Choose Fluid or Boxed layout.</p>
                        <div class="row">
                            <div class="col-4" v-for="option in widthOptions" :key="option.value">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-layout-width" :id="'layout-width-' + option.value" :value="option.value" v-model="selectedWidth">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'layout-width-' + option.value">
                                        <span class="d-flex gap-1 h-100" v-html="option.preview"></span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">{{ option.name }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Position -->
                    <div id="layout-position">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Layout Position</h6>
                        <p class="text-muted">Choose Fixed or Scrollable Layout Position.</p>
                        <div class="btn-group radio" role="group">
                            <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-fixed" value="fixed" v-model="selectedPosition">
                            <label class="btn btn-light w-sm" for="layout-position-fixed">Fixed</label>
                            <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-scrollable" value="scrollable" v-model="selectedPosition">
                            <label class="btn btn-light w-sm ms-0" for="layout-position-scrollable">Scrollable</label>
                        </div>
                    </div>

                    <!-- Topbar Color -->
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Topbar Color</h6>
                    <p class="text-muted">Choose Light or Dark Topbar Color.</p>
                    <div class="row">
                        <div class="col-4" v-for="option in topbarOptions" :key="option.value">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-topbar" :id="'topbar-color-' + option.value" :value="option.value" v-model="selectedTopbar">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'topbar-color-' + option.value">
                                    <span class="d-flex gap-1 h-100" v-html="option.preview"></span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">{{ option.name }}</h5>
                        </div>
                    </div>

                    <!-- Sidebar Size -->
                    <div id="sidebar-size">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Size</h6>
                        <p class="text-muted">Choose a size of Sidebar.</p>
                        <div class="row">
                            <div class="col-4" v-for="option in sidebarSizeOptions" :key="option.value">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size" :id="'sidebar-size-' + option.value" :value="option.value" v-model="selectedSidebarSize">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'sidebar-size-' + option.value">
                                        <span class="d-flex gap-1 h-100" v-html="option.preview"></span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">{{ option.name }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Color -->
                    <div id="sidebar-color">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Color</h6>
                        <p class="text-muted">Choose a color of Sidebar.</p>
                        <div class="row">
                            <div class="col-4" v-for="option in sidebarColorOptions" :key="option.value">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar" :id="'sidebar-color-' + option.value" :value="option.value" v-model="selectedSidebarColor">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'sidebar-color-' + option.value">
                                        <span class="d-flex gap-1 h-100" v-html="option.preview"></span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">{{ option.name }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Images -->
                    <div id="sidebar-img">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Images</h6>
                        <p class="text-muted">Choose a image of Sidebar.</p>
                        <div class="d-flex gap-2 flex-wrap img-switch">
                            <div class="form-check sidebar-setting card-radio" v-for="option in sidebarImageOptions" :key="option.value">
                                <input class="form-check-input" type="radio" name="data-sidebar-image" :id="'sidebarimg-' + option.value" :value="option.value" v-model="selectedSidebarImage">
                                <label class="form-check-label p-0 avatar-sm h-auto" :for="'sidebarimg-' + option.value">
                                    <span v-if="option.value === 'none'" class="avatar-md w-auto bg-light d-flex align-items-center justify-content-center">
                                        <i class="ri-close-fill fs-20"></i>
                                    </span>
                                    <img v-else :src="getImage('backend/images/sidebar/' + option.image)" alt="" class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Color -->
                    <div id="primary-color">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Primary Color</h6>
                        <p class="text-muted">Choose a color of Primary.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check sidebar-setting card-radio" v-for="color in primaryColors" :key="color.value">
                                <input class="form-check-input" type="radio" name="data-theme-colors" :id="'themeColor-' + color.value" :value="color.value" v-model="selectedPrimaryColor">
                                <label class="form-check-label avatar-xs p-0" :for="'themeColor-' + color.value" :style="{ background: color.color }"></label>
                            </div>
                        </div>
                    </div>

                    <!-- Preloader -->
                    <div id="preloader-menu">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Preloader</h6>
                        <p class="text-muted">Choose a preloader.</p>
                        <div class="row">
                            <div class="col-4" v-for="option in preloaderOptions" :key="option.value">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-preloader" :id="'preloader-view-' + option.value" :value="option.value" v-model="selectedPreloader">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" :for="'preloader-view-' + option.value">
                                        <span class="d-flex gap-1 h-100" v-html="option.preview"></span>
                                        <div v-if="option.value === 'enable'" id="status" class="d-flex align-items-center justify-content-center" style="position: absolute; inset: 0;">
                                            <div class="spinner-border text-primary avatar-xxs m-auto" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">{{ option.name }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <button class="btn btn-light w-100 mt-3" @click="resetTheme">Reset to Default</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch, onMounted } from 'vue';
    import { useBase } from '@/lib';

    const { getImage } = useBase();

    // State
    const activePanel = ref(false);

    // Layout Settings
    const selectedLayout = ref('vertical');
    const selectedTheme = ref('default');
    const selectedColorScheme = ref('light');
    const showUserProfile = ref(true);
    const selectedVisibility = ref('show');
    const selectedWidth = ref('fluid');
    const selectedPosition = ref('fixed');
    const selectedTopbar = ref('light');
    const selectedSidebarSize = ref('lg');
    const selectedSidebarColor = ref('dark');
    const selectedSidebarImage = ref('none');
    const selectedPrimaryColor = ref('default');
    const selectedPreloader = ref('disable');

    // Layout Options
    const layouts = [
        {
            value: 'vertical',
            name: 'Vertical',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'horizontal',
            name: 'Horizontal',
            preview: `
            <span class="d-flex h-100 flex-column gap-1">
                <span class="bg-light d-flex p-1 gap-1 align-items-center">
                    <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                    <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                    <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                </span>
                <span class="bg-light d-block p-1"></span>
                <span class="bg-light d-block p-1 mt-auto"></span>
            </span>
        `
        },
        {
            value: 'twocolumn',
            name: 'Two Column',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1">
                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'semibox',
            name: 'Semi Box',
            preview: `
            <span class="flex-shrink-0 p-1">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column pt-1 pe-2">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Theme Options
    const themes = [
        { value: 'default', name: 'Default', image: 'default.png' },
        { value: 'saas', name: 'Sass', image: 'saas.png' },
        { value: 'corporate', name: 'Corporate', image: 'corporate.png' },
        { value: 'galaxy', name: 'Galaxy', image: 'galaxy.png' },
        { value: 'material', name: 'Material', image: 'material.png' },
        { value: 'creative', name: 'Creative', image: 'creative.png' },
        { value: 'minimal', name: 'Minimal', image: 'minimal.png' },
        { value: 'modern', name: 'Modern', image: 'modern.png' },
        { value: 'interactive', name: 'Interactive', image: 'interactive.png' },
        { value: 'classic', name: 'Classic', image: 'classic.png' },
        { value: 'vintage', name: 'Vintage', image: 'vintage.png' },
    ];

    // Color Schemes
    const colorSchemes = [
        {
            value: 'light',
            name: 'Light',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'dark',
            name: 'Dark',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-dark d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-dark d-block p-1"></span>
                    <span class="bg-dark d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Visibility Options
    const visibilityOptions = [
        {
            value: 'show',
            name: 'Show',
            preview: `
            <span class="flex-shrink-0 p-1">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column pt-1 pe-2">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'hidden',
            name: 'Hidden',
            preview: `
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column pt-1 px-2">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Width Options
    const widthOptions = [
        {
            value: 'fluid',
            name: 'Fluid',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'boxed',
            name: 'Boxed',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column border-start border-end">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Topbar Options
    const topbarOptions = [
        {
            value: 'light',
            name: 'Light',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'dark',
            name: 'Dark',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-primary d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Sidebar Size Options
    const sidebarSizeOptions = [
        {
            value: 'lg',
            name: 'Default',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'md',
            name: 'Compact',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'sm',
            name: 'Small (Icon)',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1">
                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'sm-hover',
            name: 'Small Hover',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1">
                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Sidebar Color Options
    const sidebarColorOptions = [
        {
            value: 'light',
            name: 'Light',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-white border-end d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'dark',
            name: 'Dark',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-primary d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'gradient',
            name: 'Gradient',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-vertical-gradient d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Sidebar Image Options
    const sidebarImageOptions = [
        { value: 'none', name: 'None' },
        { value: 'img-1', image: 'img-1.jpg' },
        { value: 'img-2', image: 'img-2.jpg' },
        { value: 'img-3', image: 'img-3.jpg' },
        { value: 'img-4', image: 'img-4.jpg' }
    ];

    // Primary Colors
    const primaryColors = [
        { value: 'default', color: '#405189' },
        { value: 'green', color: '#2ab57d' },
        { value: 'purple', color: '#6f42c1' },
        { value: 'blue', color: '#0d6efd' }
    ];

    // Preloader Options
    const preloaderOptions = [
        {
            value: 'enable',
            name: 'Enable',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        },
        {
            value: 'disable',
            name: 'Disable',
            preview: `
            <span class="flex-shrink-0">
                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                </span>
            </span>
            <span class="flex-grow-1">
                <span class="d-flex h-100 flex-column">
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                </span>
            </span>
        `
        }
    ];

    // Methods
    const togglePanel = () => {
        activePanel.value = !activePanel.value;
        document.body.classList.toggle('offcanvas-open');
    };

    const applyTheme = () => {
        const html = document.documentElement;

        // Apply all settings
        html.setAttribute('data-layout', selectedLayout.value);
        html.setAttribute('data-theme', selectedTheme.value);
        html.setAttribute('data-bs-theme', selectedColorScheme.value);
        html.setAttribute('data-sidebar-visibility', selectedVisibility.value);
        html.setAttribute('data-layout-width', selectedWidth.value);
        html.setAttribute('data-layout-position', selectedPosition.value);
        html.setAttribute('data-topbar', selectedTopbar.value);
        html.setAttribute('data-sidebar-size', selectedSidebarSize.value);
        html.setAttribute('data-sidebar', selectedSidebarColor.value);
        html.setAttribute('data-sidebar-image', selectedSidebarImage.value);
        html.setAttribute('data-theme-colors', selectedPrimaryColor.value);
        html.setAttribute('data-preloader', selectedPreloader.value);

        // Dark mode class
        document.body.classList.toggle('dark', selectedColorScheme.value === 'dark');

        // Sidebar user profile
        const sidebarUser = document.querySelector('.sidebar-user');
        if (sidebarUser) {
            sidebarUser.style.display = showUserProfile.value ? 'block' : 'none';
        }

        // Save to localStorage
        localStorage.setItem('layout', selectedLayout.value);
        localStorage.setItem('theme', selectedTheme.value);
        localStorage.setItem('colorScheme', selectedColorScheme.value);
        localStorage.setItem('sidebarVisibility', selectedVisibility.value);
        localStorage.setItem('layoutWidth', selectedWidth.value);
        localStorage.setItem('layoutPosition', selectedPosition.value);
        localStorage.setItem('topbar', selectedTopbar.value);
        localStorage.setItem('sidebarSize', selectedSidebarSize.value);
        localStorage.setItem('sidebar', selectedSidebarColor.value);
        localStorage.setItem('sidebarImage', selectedSidebarImage.value);
        localStorage.setItem('primaryColor', selectedPrimaryColor.value);
        localStorage.setItem('preloader', selectedPreloader.value);
        localStorage.setItem('showUserProfile', JSON.stringify(showUserProfile.value));
    };

    const resetTheme = () => {
        selectedLayout.value = 'vertical';
        selectedTheme.value = 'default';
        selectedColorScheme.value = 'light';
        showUserProfile.value = true;
        selectedVisibility.value = 'show';
        selectedWidth.value = 'fluid';
        selectedPosition.value = 'fixed';
        selectedTopbar.value = 'light';
        selectedSidebarSize.value = 'lg';
        selectedSidebarColor.value = 'dark';
        selectedSidebarImage.value = 'none';
        selectedPrimaryColor.value = 'default';
        selectedPreloader.value = 'disable';
        applyTheme();
    };

    // Load saved settings
    const loadSavedSettings = () => {
        const savedLayout = localStorage.getItem('layout');
        const savedTheme = localStorage.getItem('theme');
        const savedColorScheme = localStorage.getItem('colorScheme');
        const savedVisibility = localStorage.getItem('sidebarVisibility');
        const savedWidth = localStorage.getItem('layoutWidth');
        const savedPosition = localStorage.getItem('layoutPosition');
        const savedTopbar = localStorage.getItem('topbar');
        const savedSidebarSize = localStorage.getItem('sidebarSize');
        const savedSidebar = localStorage.getItem('sidebar');
        const savedSidebarImage = localStorage.getItem('sidebarImage');
        const savedPrimaryColor = localStorage.getItem('primaryColor');
        const savedPreloader = localStorage.getItem('preloader');
        const savedShowUserProfile = localStorage.getItem('showUserProfile');

        if (savedLayout) selectedLayout.value = savedLayout;
        if (savedTheme) selectedTheme.value = savedTheme;
        if (savedColorScheme) selectedColorScheme.value = savedColorScheme;
        if (savedVisibility) selectedVisibility.value = savedVisibility;
        if (savedWidth) selectedWidth.value = savedWidth;
        if (savedPosition) selectedPosition.value = savedPosition;
        if (savedTopbar) selectedTopbar.value = savedTopbar;
        if (savedSidebarSize) selectedSidebarSize.value = savedSidebarSize;
        if (savedSidebar) selectedSidebarColor.value = savedSidebar;
        if (savedSidebarImage) selectedSidebarImage.value = savedSidebarImage;
        if (savedPrimaryColor) selectedPrimaryColor.value = savedPrimaryColor;
        if (savedPreloader) selectedPreloader.value = savedPreloader;
        if (savedShowUserProfile) showUserProfile.value = JSON.parse(savedShowUserProfile);
    };

    // Watch all changes
    watch([
        selectedLayout,
        selectedTheme,
        selectedColorScheme,
        showUserProfile,
        selectedVisibility,
        selectedWidth,
        selectedPosition,
        selectedTopbar,
        selectedSidebarSize,
        selectedSidebarColor,
        selectedSidebarImage,
        selectedPrimaryColor,
        selectedPreloader
    ], applyTheme, { deep: true });

    // Lifecycle
    onMounted(() => {
        loadSavedSettings();
        applyTheme();
    });
</script>

<style scoped>
    .customizer-setting {
        position: fixed;
        right: 20px;
        bottom: 100px;
        z-index: 1000;
    }

    .offcanvas {
        position: fixed;
        right: -500px;
        top: 0;
        bottom: 0;
        width: 400px;
        background: #fff;
        z-index: 1050;
        transition: right 0.3s ease;
        box-shadow: -2px 0 8px rgba(0,0,0,0.1);
    }
    .offcanvas.show {
        right: 0;
    }

    /* Card Radio */
    .card-radio {
        background-color: #fff;
        border: 2px solid #e9ebec;
        border-radius: 6px;
        padding: 4px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .card-radio:hover {
        border-color: #405189;
    }

    .card-radio .form-check-input:checked + .form-check-label {
        border-color: #405189;
    }

    .card-radio .form-check-input {
        display: none;
    }

    .card-radio .form-check-label {
        cursor: pointer;
        display: block;
    }

    .avatar-md {
        height: 60px;
        width: 100%;
    }

    .avatar-xs {
        height: 30px;
        width: 30px;
    }

    .avatar-sm {
        height: 50px;
        width: 50px;
    }

    .avatar-xxs {
        height: 24px;
        width: 24px;
    }

    .bg-vertical-gradient {
        background: linear-gradient(135deg, #405189, #2a3a6a);
    }

    .bg-vertical-gradient-2 {
        background: linear-gradient(135deg, #2ab57d, #1a8a5a);
    }

    .bg-vertical-gradient-3 {
        background: linear-gradient(135deg, #6f42c1, #4a2a8a);
    }

    .bg-vertical-gradient-4 {
        background: linear-gradient(135deg, #0d6efd, #0a4aad);
    }

    .material-shadow {
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .offcanvas-open {
        overflow: hidden;
    }
</style>