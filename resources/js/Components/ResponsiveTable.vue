<template>
    <div class="w-full">
        <table class="table-auto w-full border-collapse text-sm" :class="{
            'table': desktopOnly,
            'hidden sm:table': ! desktopOnly,
        }">
            <thead>
                <tr class="border-b-2">
                    <th v-if="selected" class="w-20 font-bold">
                        <Checkbox name="selected_all" v-model:checked="selectAll" />
                    </th>
                    <th class="table-cell border-b border-slate-100 p-4 px-6 text-slate-500 text-left font-bold" v-for="(header, key) in headers">
                        <span v-if="isString(header)">{{ header }}</span>
                        <span v-else>{{ header.label }}</span>
                    </th>
                    <th class="table-cell border-b border-slate-100 p-4 px-6 text-slate-500 font-normal text-left w-20" v-if="$slots.actions"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-100 odd:bg-gray-50 w-full" v-for="(row, index) in data">
                    <td v-if="selected" class="table-cell border-b border-slate-100 p-4 px-6 text-slate-500 w-20">
                        <Checkbox :name="`selected_${index}`" :value="row.id" v-model:checked="selectedValues" />
                    </td>
                    <td v-for="(header, key) in headers" class="table-cell border-b border-slate-100 p-4 px-6 text-slate-500">
                        <span v-if="isString(header)">{{ row[key] }}</span>
                        <span v-else-if="header.component">
                            <slot :row="row" :component="header.component" name="component"/>
                        </span>
                        <span v-else>{{ header.displayAs(row) }}</span>
                    </td>
                    <td v-if="$slots.actions" class="table-cell border-b border-slate-100 p-4 px-6 text-slate-500 w-20">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex">
                                    <button type="button" class="p-2 bg-gray-800 text-white rounded">
                                        Actions
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <slot :row="row" name="actions" />
                            </template>
                        </Dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="sm:hidden text-slate-500 text-sm overflow-hidden" v-if="! desktopOnly">
        <div v-for="(row, index) in data" class="overflow-hidden">
            <div class="flex justify-between items-center p-4 px-6 border-b border-slate-100 bg-gray-50">
                <div class="flex items-center space-x-2">
                    <Checkbox :name="`selected_${index}`" :value="row.id" v-model:checked="selectedValues" v-if="selected" />
                    <span class="font-bold text-lg">#{{index+1}}</span>
                </div>
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <span class="inline-flex">
                            <button type="button" class="p-2 bg-gray-800 text-white rounded">
                                Actions
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <slot :row="row" name="actions" />
                    </template>
                </Dropdown>
            </div>
            <div v-for="(header, key) in headers" class="grid grid-cols-2 gap-2 p-4 px-6 border-b border-slate-100">
                <span class="text-slate-600">
                    <span v-if="isString(header)">{{ header }}:</span>
                    <span v-else>{{ header.label }}:</span>
                </span>
                <span class="hyphens-auto block">
                    <span v-if="isString(header)">{{ row[key] }}</span>
                    <span v-else-if="header.component">
                        <slot :row="row" :component="header.component" name="component"/>
                    </span>
                    <span v-else>{{ header.displayAs(row) }}</span>
                </span>
            </div>
        </div>
    </div>
    <div class="w-full overflow-hidden" v-if="$slots.footer">
        <slot name="footer"/>
    </div>
</template>

<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {computed, ref, watch} from "vue";
import {collect} from "collect.js";

const props = defineProps({
    headers: {
        type: Object,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    selected: {
        type: Array,
        default: () => null,
    },
    desktopOnly: {
        type: Boolean,
        default: false,
    },
});

const selectAll = ref(false);
watch(selectAll, (value) => {
    if (value) {
        selectedValues.value = collect(props.data).pluck('id').all();
    } else {
        selectedValues.value = [];
    }
})

const emit = defineEmits(['update:selected']);

const selectedValues = computed({
    get() {
        return props.selected;
    },

    set(val) {
        emit('update:selected', val);
    },
});

function isString(value) {
    return typeof value === 'string';
}
</script>
