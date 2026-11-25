<script setup>
    import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';

    const props = defineProps({
        name: String,

        value: {
            type: [String, Date],
            default: '',
        },

        modelValue: {
            type: [String, Date],
            default: '',
        },

        icon: String,

        input_class: {
            type: String,
            default: 'form-control',
        },

        view_mode: {
            type: String,
            default: 'years',
        },

        id: {
            type: [String, Boolean, Number],
            default: false,
        },

        readonly: {
            type: [String, Boolean, Number],
            default: false,
        },

        editable: {
            type: Boolean,
            default: false,
        },

        placeholder: {
            type: String,
            default: 'Select Month',
        },

        format: {
            type: String,
            default: 'yy-mm',
        },

        validate: {
            type: [String, Object],
            default: '',
        },

        validation_name: String,
        vTooltipRight: Function,
        dataVvAs: String,

        disabled: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits([
        'update:value',
        'update:modelValue',
        'input',
        'change',
    ]);

    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];

    const today = new Date();

    const inputId = ref(
        props.id || `month-picker-${Math.random().toString(36).substring(2, 9)}`
    );

    const isOpen = ref(false);

    const currentYear = ref(today.getFullYear());

    const selectedMonth = ref(null);
    const selectedYear = ref(null);

    const tempMonth = ref(null);
    const tempYear = ref(null);

    const pickerRef = ref(null);

    const currentValue = computed(() => {
        return props.value || props.modelValue || '';
    });

    const parseDate = (value) => {
        if (!value) return null;

        if (value instanceof Date) {
            return {
                month: value.getMonth(),
                year: value.getFullYear(),
            };
        }

        const match = String(value).match(/^(\d{4})-(\d{1,2})/);

        if (match) {
            return {
                year: Number(match[1]),
                month: Number(match[2]) - 1,
            };
        }

        return null;
    };

    const initialDate = parseDate(currentValue.value);

    if (initialDate) {
        selectedMonth.value = initialDate.month;
        selectedYear.value = initialDate.year;
        currentYear.value = initialDate.year;
    }

    const displayValue = computed(() => {
        if (selectedMonth.value === null || selectedYear.value === null) {
            return '';
        }

        return `${months[selectedMonth.value]} ${selectedYear.value}`;
    });

    const previousYear = () => {
        currentYear.value--;
    };

    const nextYear = () => {
        currentYear.value++;
    };

    const selectMonth = (index) => {

        selectedMonth.value = index;
        selectedYear.value = currentYear.value;

        const value = formatValue(index, currentYear.value);

        emit('update:value', value);
        emit('update:modelValue', value);
        emit('input', value);
        emit('change', value);

        isOpen.value = false;
    };

    const isSelected = (index) => {
        return (
            tempMonth.value === index &&
            tempYear.value === currentYear.value
        );
    };

    const isToday = (index) => {
        return (
            today.getMonth() === index &&
            today.getFullYear() === currentYear.value
        );
    };

    const formatValue = (month, year) => {
        const mm = String(month + 1).padStart(2, '0');

        if (props.format === 'mm/yy') {
            return `${mm}/${year}`;
        }

        if (props.format === 'mm-yyyy') {
            return `${mm}-${year}`;
        }

        if (props.format === 'yyyy/mm') {
            return `${year}/${mm}`;
        }

        return `${year}-${mm}`;
    };

    const openPicker = () => {
        if (props.disabled || props.readonly) return;

        const parsed = parseDate(currentValue.value);

        tempMonth.value = parsed ? parsed.month : today.getMonth();
        tempYear.value = parsed ? parsed.year : today.getFullYear();

        currentYear.value = tempYear.value;

        isOpen.value = true;
    };

    const selectToday = () => {
        currentYear.value = today.getFullYear();
        selectMonth(today.getMonth());
    };

    const handleOutsideClick = (event) => {
        if (
            pickerRef.value &&
            !pickerRef.value.contains(event.target)
        ) {
            isOpen.value = false;
        }
    };

    const handleKeydown = (event) => {
        if (event.key === 'Escape') {
            isOpen.value = false;
        }
    };

    watch(currentValue, (value) => {
        const parsed = parseDate(value);

        if (parsed) {
            selectedMonth.value = parsed.month;
            selectedYear.value = parsed.year;
            currentYear.value = parsed.year;
        } else {
            selectedMonth.value = null;
            selectedYear.value = null;
        }
    });

    onMounted(() => {
        document.addEventListener('click', handleOutsideClick);
        document.addEventListener('keydown', handleKeydown);
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleOutsideClick);
        document.removeEventListener('keydown', handleKeydown);
    });

</script>

<template>
    <div class="month-picker-wrapper position-relative" ref="pickerRef">

        <div class="month-picker-input d-flex align-items-center" :class="{'month-picker-active': isOpen,'month-picker-disabled': disabled }" @click="openPicker">
            <i :class="icon || 'bx bx-calendar'" class="month-picker-icon"></i>
            <input type="text" autocomplete="off" :id="inputId" :name="name" :value="displayValue" :placeholder="placeholder" :readonly="true" :disabled="disabled" :data-vv-as="validation_name" :class="input_class" v-validate="validate"/>
            <i class="bx bx-chevron-down month-picker-arrow"></i>
        </div>

        <Transition name="month-picker">
            <div v-if="isOpen" class="month-picker-dropdown shadow-lg">
                <div class="month-picker-header">
                    <button type="button" class="year-nav-btn" @click="previousYear"><i class="bx bx-chevron-left"></i></button>
                    <div class="year-title">{{ currentYear }}<i class="bx bx-chevron-down"></i></div>
                    <button type="button" class="year-nav-btn" @click="nextYear"><i class="bx bx-chevron-right"></i></button>
                </div>

                <div class="month-grid">
                    <button v-for="(month, index) in months" :key="month" type="button" class="month-item" :class="{ 'month-selected': isSelected(index),'month-today': isToday(index)}" @click="selectMonth(index)">
                        <span class="month-name">{{ month.substring(0, 3) }}</span>
                        <span class="month-year">{{ currentYear }}</span>
                        <i v-if="isSelected(index)" class="bx bx-check-circle selected-check"></i>
                    </button>
                </div>

                <div class="month-picker-footer">
                    <div class="today-info" @click="selectToday">
                        <i class="bx bx-calendar"></i>
                        <span>Today: {{ months[today.getMonth()].substring(0, 3) }}{{ today.getDate() }},{{ today.getFullYear() }}</span>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>

    .month-picker-wrapper { width: 100%; }

    /* Input */
    .month-picker-input { position: relative; cursor: pointer; }

    .month-picker-input input {
        cursor: pointer; padding: 0 38px; height: 38px;
        border-radius: 6px; transition: all .2s ease;
    }

    .month-picker-input input:focus { box-shadow: none; }

    .month-picker-icon, .month-picker-arrow {
        position: absolute; color: #64748b;
    }

    .month-picker-icon { left: 12px; font-size: 18px; z-index: 2; }
    .month-picker-arrow { right: 10px; font-size: 21px; }

    .month-picker-active input {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,.08) !important;
    }

    .month-picker-disabled { opacity: .6; cursor: not-allowed; }

    /* Dropdown */
    .month-picker-dropdown {
        position: fixed;
        width: 320px;
        max-width: calc(100vw - 16px);
        max-height: calc(100vh - 16px);
        overflow-y: auto;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        z-index: 99999;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    /* Header */
    .month-picker-header {
        display: flex; align-items: center;
        justify-content: space-between; padding: 12px 14px;
    }

    .year-title {
        display: flex; align-items: center; gap: 6px;
        font-size: 18px; font-weight: 700; color: #172033;
    }

    .year-nav-btn {
        width: 34px; height: 34px; border: 0;
        border-radius: 7px; background: #f1f5f9;
        color: #334155; font-size: 21px;
        display: flex; align-items: center; justify-content: center;
        transition: all .2s ease;
    }

    .year-nav-btn:hover { background: #e2e8f0; color: #2563eb; }

    /* Month Grid */
    .month-grid {
        display: grid; grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 7px; padding: 5px 12px 14px;
    }

    .month-item {
        position: relative; min-height: 57px; padding: 5px 2px;
        border: 1px solid #e2e8f0; border-radius: 7px;
        background: #fff; color: #172033; cursor: pointer;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 2px; transition: all .2s ease;
    }

    .month-item:hover { border-color: #2563eb; background: #eff6ff; }

    .month-name { font-size: 14px; font-weight: 600; }
    .month-year { font-size: 11px; color: #64748b; }

    /* Selected & Today */
    .month-selected {
        background: #2563eb !important;
        border-color: #2563eb !important;
        color: #fff !important;
    }

    .month-selected .month-year { color: #dbeafe; }
    .month-selected:hover { background: #1d4ed8 !important; }

    .month-today:not(.month-selected) {
        border-color: #2563eb; background: #f8faff;
    }

    .selected-check {
        position: absolute; top: 2px; right: 3px;
        font-size: 13px; color: #fff;
    }

    /* Footer */
    .month-picker-footer {
        display: flex; align-items: center;
        justify-content: space-between; gap: 5px;
        padding: 10px 12px; border-top: 1px solid #e2e8f0;
    }

    .today-info {
        display: flex; align-items: center; gap: 5px;
        color: #64748b; font-size: 10px;
        cursor: pointer; white-space: nowrap;
    }

    .today-info i { font-size: 15px; }
    .today-info:hover { color: #2563eb; }

    /* Responsive */
    @media (max-width: 480px) {
        .month-picker-dropdown { width: 100%; min-width: 0; }
        .month-grid { gap: 6px; padding: 5px 9px 12px; }
        .month-item { min-height: 52px; }
        .month-name { font-size: 13px; }
        .month-year { font-size: 10px; }
        .month-picker-footer { padding: 9px; }
        .today-info { font-size: 9px; }
    }

</style>