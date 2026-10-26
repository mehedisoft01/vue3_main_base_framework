<script setup>
    import {dataTable,fromModal,tableTop,fileUpload } from '@/components';

    import {ref, onMounted} from 'vue';
    import {useStore} from 'vuex';
    const store = useStore();
    import {useBase, useHttp, appStore} from '@/lib';

    const {getDependency, submitForm, editData, deleteRecord} = {...useHttp()};
    const {_l,can,formFilter, formObject, openModal, closeModal, useGetters, dataList, httpRequest, pageDependencies, updateId,statusBadge,getImage,changeStatus} = {
        ...useBase(),
        ...useHttp(),
        ...appStore(),
        ...appStore().useGetters('dataList', 'httpRequest', 'pageDependencies', 'updateId')
    };

    const tableHeaders = ref(["#", "division_name","division_code", "status", "actions"]);
    const {getDataList, httpReq} = useHttp();

    onMounted(() => {
        getDataList();
        getDependency();
    });
</script>

<template>
    <dataTable :headings="tableHeaders" :setting="true">
        <template v-slot:tableTop>
            <tableTop :defaultAddButton="can('division.store')" :defaultObject="{parent_id: ''}"></tableTop>
        </template>
        <template v-slot:data>
            <tr v-for="(item, index) in dataList.data" :key="item.id">
                <td>{{ index + 1 }}</td>
                <td>{{ item.division_name }}</td>
                <td>{{ item.division_code }}</td>
                <td>
                    <a @click="changeStatus({obj:item})" class="pointer" v-html="statusBadge(item.status)"></a>
                </td>
                <td>
                    <a @click="editData({data:item, id:item.id, modal:'fromModal'})" v-if="can('division.update')" class="btn btn-outline-secondary action me-2">
                        <i class='bx bxs-edit text-warning'></i>
                    </a>
                    <a @click="deleteRecord({targetId:item.id,listIndex:index, listObject:dataList.data})" v-if="can('division.destroy')" class="btn btn-outline-secondary action">
                        <i class='bx bxs-trash text-danger'></i>
                    </a>
                </td>
            </tr>
        </template>
        <fromModal title="Add Division" @submit="submitForm({modal: 'fromModal',callback: function (retData) {Object.assign(formObject, {});getDataList(); }})">
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{_l('division_name')}} : </strong></label>
                <div class="col-md-8">
                    <input type="text" v-model="formObject.division_name" class="form-control" name="division_name" v-validate="'required'"  placeholder="Enter Division Name"/>
                </div>
            </div>
        </fromModal>
    </dataTable>
</template>

