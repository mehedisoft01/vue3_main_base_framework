<script setup>
    import {dataTable,fromModal,tableTop} from '@/components';

    import {ref, onMounted} from 'vue';
    import {useStore} from 'vuex';
    const store = useStore();
    import {useBase, useHttp, appStore} from '@/lib';

    const {getDependency, submitForm, editData, deleteRecord} = {...useHttp()};
    const {_l,can,formFilter, formObject, openModal, closeModal, useGetters, dataList, httpRequest, pageDependencies, updateId,statusBadge,getImage,changeStatus,handleSelectAll,deleteAllRecords} = {
        ...useBase(),
        ...useHttp(),
        ...appStore(),
        ...appStore().useGetters('dataList', 'httpRequest', 'pageDependencies', 'updateId')
    };

    const tableHeaders = ref(["#",{name: '', listObject: dataList}, "example_name", "example_date","month","remarks","status", "Actions"]);
    const {getDataList, httpReq,urlGenerate} = useHttp();

    onMounted(() => {
        getDataList();
        getDependency({dependency : []});
    });

</script>


<template>
    <dataTable :headings="tableHeaders" :setting="true">
        <template v-slot:tableTop>
            <tableTop :defaultAddButton="false">
                <template #action>
                    <router-link v-if="can('example.store')" to="/example/create" class="btn btn-outline-secondary radius-30 mt-2 mt-lg-0 text-uppercase"> <i class="bx bxs-plus-circle"></i>{{_l('add_new')}}</router-link>
                </template>
                <template v-slot:filter>
                </template>
            </tableTop>
        </template>
        <template v-slot:topRight v-if="dataList.data !== undefined">
            <a class="btn btn-sm btn-outline-danger radius-30 text-uppercase" @click="deleteAllRecords({dataObject:dataList.data})" v-if="dataList.data.some(each => parseInt(each.checked) === 1)">Delete All</a>
        </template>
        <template v-slot:data>
            <tr v-for="(item, index) in dataList.data" :key="item.id">
                <td>{{ parseInt(dataList.from) + index}}</td>
                <td class="checkbox"><input :checked="item.checked" @change="handleSelectAll($event, [item])" class="form-check-input me-3 pointer" type="checkbox"/></td>
                <td>{{ item.example_name }}</td>
                <td>{{ item.example_date }}</td>
                <td>{{ item.month }}</td>
                <td>{{ item.remarks }}</td>
                <td>
                    <a @click="changeStatus({obj:item})" class="pointer" v-html="statusBadge(item.status)"></a>
                </td>
                <td>

                    <router-link v-if="can('example.show')" class="btn btn-outline-secondary action me-1" :to="`/example/details/${item.id}`">
                        <i class="bx bxs-show text-primary"></i>
                    </router-link>

                    <router-link v-if="can('example.update')" class="btn btn-outline-secondary action me-1" :to="`/example/edit/${item.id}`">
                        <i class="bx bxs-edit text-warning"></i>
                    </router-link>

                    <a v-if="can('example.destroy')"  @click="deleteRecord({targetId:item.id,listIndex:index, listObject:dataList.data})" class="btn btn-outline-secondary action me-1">
                        <i class='bx bxs-trash text-danger'></i>
                    </a>

                </td>
            </tr>
        </template>
    </dataTable>
</template>