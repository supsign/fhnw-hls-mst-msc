<template>
    <div>
        <div class="font-bold text-lg mb-5">Double Degree Option</div>
        <div v-if="description" v-html="description.content" class="mb-7"></div>
        <div class="flex">
            <input
                type="checkbox"
                :value="modelValue"
                @input="$emit('update:modelValue', !modelValue)"
                class="border rounded-lg border-gray-200 h-7 shadow-md w-7 box-border"
            />
            <label class="ml-2">I'm interested in the Double-Degree option</label>
        </div>
    </div>
</template>
<script setup lang="ts">
import type { PropType } from 'vue';
import type { IText } from '../../interfaces/text.interface';

const props = defineProps({
    texts: { type: Array as PropType<Array<IText>>, required: true },
    modelValue: Boolean,
});

defineEmits(['update:modelValue']);

const description: IText | null = props.texts.find((text) => text.name === 'double_degree_description') || null;
</script>
