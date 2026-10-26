<script setup>
    import {dataTable,fromModal,tableTop } from '@/components';

    import {ref, onMounted,watch} from 'vue';
    import {useStore} from 'vuex';
    const store = useStore();
    import {useBase, useHttp, appStore} from '@/lib';

    const {getDependency, submitForm, editData, deleteRecord} = {...useHttp()};
    const {_l,can,formFilter, formObject, openModal, closeModal, useGetters, dataList, httpRequest, pageDependencies,statusBadge,changeStatus, updateId} = {
        ...useBase(),
        ...useHttp(),
        ...appStore(),
        ...appStore().useGetters('dataList', 'httpRequest', 'pageDependencies', 'updateId')
    };
    const { Config,appConfigs} = useGetters('Config','appConfigs' );

    const tableHeaders = ref(["#", "name", "email", "user_name", "create_at", "status", "actions"]);
    const {getDataList, httpReq} = useHttp();
    const settings = ref({});
    const isRole = (roleId) => {
        return Object.keys(settings.value).find(key => settings.value[key] === String(roleId));
    };

    onMounted(() => {
        getDataList();
        getDependency({dependency : ['roles','warehouses','division','district','upazila','salesman']});
        getDependency({
            dependency: {
                settings : ['division_manager_role', 'district_manager_role', 'financial_manager_role']
            },
            callback : (retData) => {
                settings.value = retData.settings;
            }
        });
    });
</script>

<template>
    <dataTable :headings="tableHeaders" :setting="true">
        <template v-slot:tableTop>
            <tableTop :defaultObject="{role_id:'',manager:'',upazila_id:'',division_id:'',district_id:'',salesman_id:''}"></tableTop>
        </template>
        <template v-slot:data>
            <tr v-for="(item, index) in dataList.data" :key="item.id">
                <td>{{index+1}}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.username }}</td>
                <td>{{ item.created_at }}</td>
                <td>
                    <a @click="changeStatus({obj:item})" class="pointer" v-html="statusBadge(item.status)"></a>

                </td>
                <td>
                    <a v-if="can('users.update')" @click="editData({data:item, id:item.id, modal:'fromModal'})" class="btn btn-outline-secondary action me-1">
                        <i class='bx bxs-edit text-warning'></i>
                    </a>
                    <a v-if="can('users.destroy')" @click="deleteRecord({targetId:item.id,listIndex:index, listObject:dataList.data})" class="btn btn-outline-secondary action">
                        <i class='bx bxs-trash text-danger'></i>
                    </a>
                </td>
            </tr>
        </template>

        <fromModal title="Add User" modal-size="modal-lg" @submit="submitForm({modal: 'fromModal',callback: function (retData) {Object.assign(formObject, {});getDataList();}})">
            <div class="row mb-2">
                <div class="col-md-4 d-flex align-items-center">
                    <strong>{{ _l('role')}} :</strong>
                </div>
                <div class="col-md-8">
                    <select v-select2 v-model="formObject.role_id" class="form-control" :data-placeholder="'--Select Role--'">
                        <option value="">Select</option>
                        <template v-for="role in pageDependencies.roles">
                            <option :value="role.id">{{role.display_name}}</option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{ _l('name')}} : </strong></label>
                <div class="col-md-8">
                    <input  type="text" v-model="formObject.name" class="form-control"/>
                </div>
            </div>
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{ _l('email')}} :</strong></label>
                <div class="col-md-8">
                    <input  type="text" v-model="formObject.email" class="form-control"/>
                </div>
            </div>
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{ _l('user_name')}} : </strong></label>
                <div class="col-md-8">
                    <input type="text" v-model="formObject.username" class="form-control"/>
                </div>
            </div>
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{ _l('password')}} : </strong></label>
                <div class="col-md-8">
                    <input type="text" v-model="formObject.password" class="form-control"/>
                </div>
            </div>
            <div class="row mb-2">
                <label class="col-md-4"><strong>{{ _l('is_super_admin')}} : </strong></label>
                <div class="col-md-8">
                    <input type="checkbox" class="boxsize" v-model="formObject.is_superadmin" :true-value="1" :false-value="0">
                </div>
            </div>
        </fromModal>
    </dataTable>
</template>
<style scoped>
    .boxsize {
        width: 20px;
        height: 20px;
    }
</style>
