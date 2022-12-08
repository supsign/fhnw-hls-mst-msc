<template>
    <div v-if="data" class="flex flex-col gap-5">
        <div class="font-bold text-lg">Master Thesis</div>
        <Select
            label="Start of MSc Thesis"
            :options="data.time_frames"
            v-model="value.start"
            option_labels="start.long_name"
        />
        <div v-if="text" v-html="text.content"></div>
        <div>
            <Select
                label="Subject of the MSc Thesis"
                :options="data.theses"
                v-model="value.theses"
                multiple
                :size="data.theses.length"
            />
        </div>
        <div>
            <label for="furtherDetails" class="bg-white px-1 text-gray-400"
                >Further Details on MSc Topic (optional)</label
            >
            <textarea
                id="furtherDetails"
                v-model="value.furtherDetails"
                class="border rounded-lg border-gray-200 shadow-md w-full py-2 px-4 text-gray-900 block box-border"
            />
        </div>
    </div>
</template>
<script setup lang="ts">
import { type PropType, computed, type WritableComputedRef } from 'vue';
import type { IText } from '../../interfaces/text.interface';
import type { IThesisDataResponse, IThesisSelection } from '../../interfaces/theses.interface';
import Select from '../base/Select.vue';

const props = defineProps({
    data: { type: Object as PropType<IThesisDataResponse>, required: true },
    modelValue: { type: Object as PropType<IThesisSelection>, required: true },
});

const emits = defineEmits(['update:modelValue']);

const text: IText | null = props.data.texts.find((text) => text.name === 'thesis_text') || null;

const value: WritableComputedRef<IThesisSelection> = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emits('update:modelValue', value);
    },
});
</script>
