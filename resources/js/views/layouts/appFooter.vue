<template>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        Copyright © {{ new Date().getFullYear() }} TMSS ICT LTD. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
    import { computed } from 'vue';
    import { appStore } from "@/lib";

    const { useGetters } = appStore();
    const { appConfigs } = useGetters('appConfigs');

    const appName = computed(() => {
        return appConfigs.value.app_name || 'CNG Showroom';
    });
</script>

<style scoped>
    .footer {
        background: #fff;
        padding: 15px 0;
        border-top: 1px solid #f1f5f7;
        color: #5b6e88;
        margin-top: auto;
    }

    body.dark .footer {
        background: #1a2634;
        border-top-color: #2a3a5c;
        color: #a8b9d1;
    }

    @media (max-width: 768px) {
        .footer {
            text-align: center;
        }
        .footer .col-sm-6 {
            text-align: center !important;
        }
    }
</style>
