<script setup>
    import { detailsComponent } from '@/components';
    import { onMounted, ref, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import { useBase, useHttp } from '@/lib';

    const route = useRoute();
    const { _l } = useBase();
    const { httpReq, urlGenerate } = useHttp();
    const detailsData = ref({});
    const items = computed(() => Array.isArray(detailsData.value.example_items) ? detailsData.value.example_items : []);

    const formatNumber = (value) => Number(value || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const details = async (id) => {
        try {
            const url = `${urlGenerate()}/${id}`;
            const retData = await httpReq({ url, loader: true });

            if (retData) {
                detailsData.value = { ...retData };
            }
        } catch (error) {
            console.error('Failed to fetch example details:', error);
        }
    };

    onMounted(() => {
        if (route.params.id) details(route.params.id);
    });

</script>

<template>
    <detailsComponent>
        <div class="main-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <div class="text-muted small mb-1"></div>
                            <h3 class="fw-bold mb-0 text-dark"><i class="bx bx-package me-2 text-primary"></i>{{_l('example_details') }}</h3>
                            <hr>
                        </div>
                        <div class="d-flex gap-2">
                            <router-link to="/example" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i>{{ _l('back_to_list') }}
                            </router-link>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="text-muted small mb-1">{{ _l('example_name') }}</div>
                                    <div class="fw-semibold">{{ detailsData.example_name || '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small mb-1">{{ _l('example_date') }}</div>
                                    <div class="fw-semibold"><i class="bx bx-calendar me-1 text-primary"></i>{{ detailsData.example_date || '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small mb-1">{{ _l('status') }}</div>
                                    <span :class="detailsData.status ? 'badge bg-success-subtle text-success' : 'badge bg-secondary-subtle text-secondary'">
                                        {{ detailsData.status ? _l('active') : _l('inactive') }}
                                    </span>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small mb-1">{{ _l('remarks') }}</div>
                                    <div class="p-3 bg-light rounded">{{ detailsData.remarks || '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bx bx-list-check me-2 text-primary"></i>{{ _l('purchase_order_line_item') }}</h6>
                            <span class="badge bg-primary-subtle text-primary">{{ items.length }} {{ _l('items') }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 60px">#</th>
                                            <th>{{ _l('item_name') }}</th>
                                            <th class="text-end">{{ _l('quantity') }}</th>
                                            <th class="text-end">{{ _l('rate') }}</th>
                                            <th class="text-end">{{ _l('amount') }}</th>
                                            <th>{{ _l('remarks') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in items" :key="item.id || index">
                                            <td class="text-center text-muted">{{ index + 1 }}</td>
                                            <td class="fw-semibold">{{ item.item_name || '—' }}</td>
                                            <td class="text-end">{{ formatNumber(item.quantity) }}</td>
                                            <td class="text-end">{{ formatNumber(item.rate) }}</td>
                                            <td class="text-end fw-semibold">{{ formatNumber(item.amount) }}</td>
                                            <td class="text-muted">{{ item.remarks || '—' }}</td>
                                        </tr>
                                        <tr v-if="!items.length">
                                            <td colspan="6" class="text-center text-danger py-5">{{ _l('no_data_available') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top p-3">
                            <div class="row justify-content-end g-3">
                                <div class="col-sm-5 col-md-3">
                                    <div class="text-muted small">{{ _l('total_quantity') }}</div>
                                    <div class="fs-5 fw-bold">{{ formatNumber(detailsData.total_qty) }}</div>
                                </div>
                                <div class="col-sm-5 col-md-3">
                                    <div class="text-muted small">{{ _l('grand_total') }}</div>
                                    <div class="fs-5 fw-bold text-primary">{{ formatNumber(detailsData.grand_total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </detailsComponent>
</template>
