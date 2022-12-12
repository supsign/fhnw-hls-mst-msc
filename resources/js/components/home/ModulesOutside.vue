<template>
    <div>
        <div class="font-bold text-lg mb-5">Modules outside the Curriculum</div>
        <div v-if="description" v-html="description.content" class="mb-5"></div>
        <div class="flex flex-col gap-5">
            <div v-for="(module, index) in modulesOutsideArray" :key="index" class="flex gap-10">
                <Input label="Module Title" v-model="module.title" />
                <NumberInput label="ECTS" type="number" :min="0" v-model="module.ects" />
                <Input label="University" v-model="module.university" />
                <div
                    @click="modulesOutsideArray.splice(index, 1)"
                    class="rounded-md cursor-pointer flex bg-red-600 h-10 shadow-sm mt-6 text-white py-1 transform px-4 transition ease-in-out duration-300 items-center hover:bg-red-700 hover:shadow-xl"
                >
                    Remove
                </div>
            </div>
            <div>
                <button
                    @click="addNewModule"
                    class="rounded-md cursor-pointer bg-blue-700 shadow-sm text-white py-1 transform px-4 transition ease-in-out duration-300 hover:bg-blue-800 hover:shadow-xl"
                >
                    New Module
                </button>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { whenever } from '@vueuse/core';
import {type PropType, type Ref, ref } from 'vue';
import type { IModuleOutside } from '../../interfaces/moduleOutside.interface';
import type { IText } from '../../interfaces/text.interface';
import Input from '../base/Input.vue';
import NumberInput from '../base/NumberInput.vue';
const props = defineProps({
    texts: { type: Array as PropType<Array<IText>>, required: true },
});
const emits = defineEmits(['updateModulesOutsideData']);
const description: IText | null = props.texts.find((text) => text.name === 'modules_outside_description') || null;
const modulesOutsideArray: Ref<Array<IModuleOutside>> = ref([]);

function addNewModule() {
    modulesOutsideArray.value.push({ title: '', ects: 0, university: '' });
}
// @ts-ignore
whenever(modulesOutsideArray.value, (value) => {
    emits('updateModulesOutsideData', value);
});
</script>
