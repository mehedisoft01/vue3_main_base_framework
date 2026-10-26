<script setup>
    import {ref, defineProps} from 'vue'
    import {appStore, useHttp, useBase} from '@/lib';

    import {useStore} from 'vuex';

    const store = useStore();

    const {_l, formFilter, useGetters, routeMeta, getDataList, getRoute} = {...useBase(), ...appStore(), ...useHttp()};
    const {dataList} = useGetters('dataList', 'httpRequest');
    const perPage = ref([10,15, 20, 50, 100, 200]);

    const props = defineProps({
        listPage: {type: Boolean, default: true},
        title: {type: String, default: ''},
    });

    const title = (props.title !== '') ? props.title : (routeMeta('title') ?? 'Data List');
    const listPage = props.listPage ?? (routeMeta('listPage') ?? true);
</script>

<template>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item">
                    <a href="#"><i class="bx bx-home-alt"></i></a>
                </li>
                <template v-if="listPage">
                    <li class="breadcrumb-item active" aria-current="page">
                        <select @change="getDataList" class="per-page-select" v-model="formFilter.per_page">
                            <option v-for="page in perPage" :key="page" :value="page">
                                {{ _l(getRoute('name')) }} {{ page }}
                            </option>
                        </select>
                    </li>
                    <li v-if="dataList.total !== undefined">
                        <span class="page_top_text">|| <b>{{ _l('total') }}</b> : {{ dataList.total }} || <b>{{ _l('showing') }}</b> : {{ dataList.from }} to {{ dataList.to }} (<b>{{ _l('per_page') }}</b> : {{ dataList.per_page }})</span>
                    </li>
                </template>
                <template v-else>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">{{_l(getRoute('name'))}}</li>
                </template>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <div class="btn-group">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
    .per-page-select{
        border: none !important;
        outline: none !important;
        background: transparent !important;
        box-shadow: none !important;

        padding: 0;
        margin: 0;

        color: #495057;
        font-size: 14px;
        font-weight: 500;

        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .per-page-select:focus{
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
</style>
