<script setup>
    import { MultipleFileUpload,fileUpload,TinyEditor } from '@/components';
    import Datepicker from "../../../plugins/datepicker.vue";
    import Monthpicker from "../../../plugins/monthpicker.vue";

    import { useRouter, useRoute } from 'vue-router';
    import { ref, onMounted,computed} from 'vue';
    import { useStore } from 'vuex';
    import { useBase, useHttp, appStore } from '@/lib';

    const router = useRouter();
    const route = useRoute();
    const store = useStore();

    const { getDependency, submitForm, editData } = useHttp();
    const { formObject, pageDependencies, _l,toaster,openModal, closeModal,addRow,deleteRow} = {
        ...useBase(),
        ...useHttp(),
        ...appStore(),
        ...appStore().useGetters('dataList', 'httpRequest', 'pageDependencies', 'updateId')
    };
    const { getDataList,httpReq,changeStatus,urlGenerate } = useHttp();

    const redirectAfterSubmit = () => {
        resetFormData();
        router.push('/example');
    };

    const resetFormData = () => {
        Object.keys(formObject.value).forEach(key => {
            formObject.value[key] = '';
        });

        formObject.value.example_items = [{ item_name: '',quantity: '',rate: '', amount: '', remarks: ''}];

        store.commit('updateId', null);
    };

    const idParam = computed(() => route.params.id || null);
    const isEdit = computed(() => !!idParam.value);

    async function editDataInformation(id) {
        try {
            const res = await httpReq({ url: `${urlGenerate()}/${id}/edit`, method: 'get', loader: true });
            const data = res.data || res;
            if (!Array.isArray(data.example_items) || data.example_items.length === 0) {
                data.example_items = [{ item_name: '',quantity: '',rate: '', amount: '', remarks: ''}];
            }
            // console.log('edit fetch', data)
            store.commit('formObject', data);
            store.commit('updateId', data.id || null);
            getDependency({ dependency: ['material','requisitions','vendors'] })
        } catch (error) {
            console.error('Error fetching edit data:', error)
        }
    }

    const calculateTotals = () => {
        let totalQty = 0;
        let grandTotal = 0;

        formObject.value.example_items.forEach(row => {
            row.amount = (Number(row.quantity) || 0) * (Number(row.rate) || 0);

            totalQty += Number(row.quantity) || 0;
            grandTotal += Number(row.amount) || 0;
        });

        formObject.value.total_qty = totalQty.toFixed(2);
        formObject.value.grand_total = grandTotal.toFixed(2);
    };

    onMounted(() => {

        formObject.value.example_items = [{ item_name: '',quantity: '',rate: '', amount: '', remarks: ''}];

        if (isEdit.value) {
            editDataInformation(idParam.value)
        } else {
            resetFormData();
        }

        getDependency({dependency : []});

    });

</script>

<template>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="main-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-package me-1"></i>{{ isEdit ? _l('example_edit_from') : _l('example_create_from') }}</h5>
                                    </div>
                                    <router-link to="/example" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i>{{ _l('back_to_list') }}</router-link>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <form @submit.prevent="submitForm({ data: formObject, callback: redirectAfterSubmit })">
                                    <div class="card border shadow-none mb-4">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold"><i class="bx bx-file me-1"></i>{{ _l('example_information')}}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3 mb-3">
                                                <div class="col">
                                                    <label class="form-label fw-semibold">{{ _l('example_name') }}</label>
                                                    <input type="text" v-model="formObject.example_name" class="form-control" v-validate="'required'" name="example_name"/>
                                                </div>
                                                <div class="col">
                                                    <label class="form-label fw-semibold">{{ _l('example_date') }}</label>
                                                    <datepicker type="text" v-model="formObject.example_date" class="form-control" v-validate="'required'" name="example_date"/>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">{{ _l('month') }}</label>
                                                    <Monthpicker v-model="formObject.month" name="month"/>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label class="form-label fw-semibold">{{ _l('remarks') }}</label>
                                                <TinyEditor type="text" v-model="formObject.remarks" class="form-control" name="remarks"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border shadow-none mb-4">
                                        <div class="card-header bg-light py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><i class="bx bx-list-check me-1"></i>{{ _l('purchase_order_line_item') }}</h6>
                                                </div>
                                                <span class="badge bg-primary">{{ formObject.example_items?.length || 0 }} Items</span>
                                            </div>
                                        </div>

                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th width="5%" class="text-center">#</th>
                                                        <th width="25%">{{ _l('item_name') }}</th>
                                                        <th width="12%">{{ _l('quantity') }}</th>
                                                        <th width="12%">{{ _l('rate') }}</th>
                                                        <th width="14%">{{ _l('amount') }}</th>
                                                        <th width="25%">{{ _l('remarks') }}</th>
                                                        <th width="7%" class="text-center">{{ _l('action') }}</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <tr v-for="(row, index) in formObject.example_items" :key="index">
                                                        <td class="text-center fw-semibold text-muted">{{ index + 1 }}</td>
                                                        <td><input type="text" v-model="row.item_name" class="form-control" placeholder="Item Name"/></td>
                                                        <td><input type="number" step="0.01" min="0" v-model="row.quantity" @input="calculateTotals()" class="form-control text-end" placeholder="0.00"/></td>
                                                        <td><input type="number" step="0.01" min="0" v-model="row.rate" @input="calculateTotals()" class="form-control text-end" placeholder="0.00"/></td>
                                                        <td><input type="number" step="0.01" min="0" v-model="row.amount" class="form-control bg-light fw-semibold text-end" placeholder="0.00" readonly/></td>
                                                        <td><input type="text" v-model="row.remarks" class="form-control" placeholder="Remarks"/></td>
                                                        <td class="text-center text-nowrap">
                                                            <button type="button" class="btn btn-sm btn-outline-success me-1" v-if="index === formObject.example_items.length - 1"
                                                                    @click="addRow(formObject.example_items,{ item_name: '',quantity: '',rate: '', amount: '', remarks: ''})">
                                                                <i class="bx bx-plus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" v-if="formObject.example_items.length > 1" @click="deleteRow(formObject.example_items, index); calculateTotals()">
                                                                <i class="bx bx-x"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- ================= ORDER SUMMARY ================= -->
                                            <div class="border-top bg-light p-3">
                                                <div class="row justify-content-end">
                                                    <div class="col-xl-5 col-lg-6 col-md-7">
                                                        <div class="bg-white border rounded shadow-sm p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                                <div class="fw-bold text-dark">
                                                                    <i class="bx bx-calculator me-1 text-primary"></i>{{ _l('example_summary')}}
                                                                </div>
                                                                <span class="badge bg-light text-primary border">{{ formObject.example_items?.length || 0 }} Items</span>
                                                            </div>
                                                            <div class="row align-items-center mb-2">
                                                                <label class="col-sm-5 col-form-label fw-semibold ">
                                                                    <i class="bx bx-package me-1"></i>{{ _l('total_quantity') }}
                                                                </label>
                                                                <div class="col-sm-7">
                                                                    <input type="number" class="form-control bg-light fw-semibold" v-model="formObject.total_qty" name="total_qty" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row align-items-center pt-2 mt-2 border-top">
                                                                <label class="col-sm-5 col-form-label fw-bold text-primary">
                                                                    <i class="bx bx-calculator me-1"></i>{{ _l('grand_total') }}
                                                                </label>

                                                                <div class="col-sm-7">
                                                                    <input type="number" step="0.01" v-model="formObject.grand_total" name="grand_total" class="form-control bg-primary bg-opacity-10 border-primary text-primary fw-bold" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <router-link to="/purchase_order" class="btn btn-light border"><i class="bx bx-x me-1"></i>{{ _l('cancel') }}</router-link>
                                        <button type="submit" class="btn btn-primary px-4"><i :class="isEdit ? 'bx bx-edit me-1' : 'bx bx-save me1'"></i>{{ isEdit ?  _l('update') : _l('save') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>