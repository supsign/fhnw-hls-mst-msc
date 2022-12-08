<template>
    <div v-if="data" class="flex flex-col gap-5 justify-center">
        <Introduction :texts="data.texts" />
        <div class="font-bold text-lg">Personal Data</div>
        <Input label="Surname" v-model="value.surname" />
        <Input label="Given Name" v-model="value.givenName" />
        <Select
            label="Start of Studies"
            :options="semesters"
            v-model="value.semester"
            @change="emits('getCourseData')"
            option_labels="long_name_with_short"
        />
        <Select
            label="Study Mode"
            :options="data.studyMode.studyModes"
            option_labels="label"
            v-model="value.studyMode"
            :tooltip="data.studyMode.tooltip"
            @change="emits('getCourseData')"
        />
        <Select
            label="Specialization"
            :options="data.specializations"
            placeholder="-- Choose Specialization --"
            v-model="value.specialization"
            @change="emits('getCourseData')"
        />
    </div>
</template>
<script setup lang="ts">
import { onBeforeMount, type PropType, ref, type WritableComputedRef, computed } from 'vue';
import axios from 'axios';
import type { IPersonalDataResponse, IPersonalData } from '../../interfaces/personal.interface';
import Input from '../base/Input.vue';
import Select from '../base/Select.vue';
import Introduction from './Introduction.vue';
import dayjs from 'dayjs';

const props = defineProps({
    modelValue: { type: Object as PropType<IPersonalData>, required: true },
});
const emits = defineEmits(['getCourseData', 'update:modelValue']);

const data = ref();

const value: WritableComputedRef<IPersonalData> = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emits('update:modelValue', value);
    },
});

onBeforeMount(async () => {
    data.value = await getPersonalData();
    prefillValues(data.value);
});

async function getPersonalData() {
    const response = await axios.get<IPersonalDataResponse>('/personaldata');
    return response.data;
}

function prefillValues(data: IPersonalDataResponse) {
    value.value.semester = data.semesters.find((semester) => semester.is_current);
    value.value.studyMode = data.studyMode.studyModes[0];
}

const semesters = computed(() => {
    const current = data.value.semesters.find((semester: any) => semester.is_current);
    return data.value.semesters.map((semester: any) => {
        if (dayjs(semester.start_date).isBefore(dayjs(current.start_date))) {
            semester.long_name_with_short += ' (replanning of already started studies)';
        }
        return semester;
    });
});
</script>
