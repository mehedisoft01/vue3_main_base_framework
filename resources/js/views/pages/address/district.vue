<script setup>
    import {dataTable,fromModal,tableTop,fileUpload } from '@/components';

    import {ref, onMounted} from 'vue';
    import {useStore} from 'vuex';
    const store = useStore();
    import {useBase, useHttp, appStore} from '@/lib';
    import ThemeSwitcher from "../../layouts/themeSwitcher.vue";

    const {getDependency, submitForm, editData, deleteRecord} = {...useHttp()};
    const {_l,can,formFilter, formObject, openModal, closeModal, useGetters, dataList, httpRequest, pageDependencies, updateId,statusBadge,getImage,changeStatus} = {
        ...useBase(),
        ...useHttp(),
        ...appStore(),
        ...appStore().useGetters('dataList', 'httpRequest', 'pageDependencies', 'updateId')
    };

    const tableHeaders = ref(["#", "district_name","division_name","district_code","status", "actions"]);
    const {getDataList, httpReq} = useHttp();

    onMounted(() => {
        getDependency({dependency : ['divisions',]});
        formFilter.value.division_id = '';
        getDataList();

    });
</script>

<template>
    <dataTable :headings="tableHeaders" :setting="true">
        <theme-switcher></theme-switcher>
        <template v-slot:tableTop>
            <tableTop :defaultAddButton="can('district.store')" :defaultObject="{division_id: ''}">
                <div class="col-md-3">
                    <select v-select2 v-model="formFilter.division_id" class="form-control radius-30" :data-placeholder="'-- Select Division --'">
                        <template v-for="item in pageDependencies.divisions">
                            <option :value="item.id">{{ item.division_name }}</option>
                        </template>
                    </select>
                </div>
            </tableTop>
        </template>
        <template v-slot:data>
            <tr v-for="(item, index) in dataList.data" :key="item.id">
                <td>{{ parseInt(dataList.from) + index}}</td>
                <td>{{ item.district_name }}</td>
                <td>{{ item.division?.division_name || '-' }}</td>
                <td>{{item.district_code || '-' }}</td>
                <td>
                    <a @click="changeStatus({obj:item})" class="pointer" v-html="statusBadge(item.status)"></a>
                </td>
                <td>
                    <a @click="editData({data:item, id:item.id, modal:'fromModal'})" v-if="can('district.update')" class="btn btn-outline-secondary action me-2">
                        <i class='bx bxs-edit text-warning'></i>
                    </a>
                    <a @click="deleteRecord({targetId:item.id,listIndex:index, listObject:dataList.data})" v-if="can('district.destroy')" class="btn btn-outline-secondary action">
                        <i class='bx bxs-trash text-danger'></i>
                    </a>
                </td>
            </tr>
        </template>
        <fromModal @submit="submitForm({ modal: 'fromModal', callback: function (retData) { Object.assign(formObject, {}); getDataList(); }})">
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{_l('division_name')}}: </strong></label>
                <div class="col-md-8">
                    <select v-select2 v-model="formObject.division_id" class="form-control" v-validate="'required'" :data-placeholder="'--Select Division--'">
                        <template v-for="item in pageDependencies.divisions">
                            <option :value="item.id">{{ item.division_name }}</option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{_l('district_name')}} : </strong></label>
                <div class="col-md-8">
                    <input type="text" v-model="formObject.district_name" class="form-control" name="district_name" v-validate="'required'"  placeholder="Enter District Name"/>
                </div>
            </div>

        </fromModal>
    </dataTable>
</template>

