<script setup>
    import {ref, onMounted,watch,computed} from 'vue'
    import { useBase, useHttp, appStore } from '@/lib';
    let {_l,formFilter, useGetters,getDependency,pageDependencies, getDataList,dataList, httpRequest, can} = {...useBase(), ...useHttp(), ...appStore(), ...appStore().useGetters('httpRequest','pageDependencies')};
    const { Config,appConfigs} = useGetters('Config','appConfigs' );

    // =================================================
    //  Chart Configuration (Series & Chart Options)
    // =================================================
    const costAnalysisChart = ref({
        series: [],
        chartOptions: {}
    });

    // =================================================
    //  Dashboard KPI & Summary Variables
    // =================================================
    const total_sectors = ref(0);


    const setDashboardData = (dashboardData) => {

        const dashboard = dashboardData.dashboard ?? {};

        total_sectors.value = dashboard.total_sectors ?? 0;

        costAnalysisChart.value = dashboard.costAnalysisChart ?? {
            series: [],
            chartOptions: {}
        };

    };


    onMounted(async () => {
        await getDependency({
            dependency: ['centers']
        });

        getDataList({
            callback: (retData) => {
                setDashboardData(retData);
            }
        });
    });


</script>


<template>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid px-4 py-3">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="bx bx-grid-alt text-primary me-2"></i>
                            Dashboard Overview
                        </h4>
                        <p class="text-muted mb-0">
                            Welcome to your management system. Monitor your activities and manage everything from one place.
                        </p>
                    </div>
                </div>

            </div>

            <div class="page_loader" v-if="httpRequest">
                <i class="bx bx-loader bx-spin text-warning"></i>
            </div>
        </div>
    </div>
</template>
