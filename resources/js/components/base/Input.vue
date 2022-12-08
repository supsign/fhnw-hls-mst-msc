<template>
    <div>
        <label v-if="label" for="input" class="px-1 bg-white text-gray-400">{{ label }}</label>
        <input
            id="input"
            class="block py-2 px-4 w-full box-border border rounded-lg border-gray-200 shadow-md text-gray-900"
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
            v-bind="$attrs"
        />
    </div>
</template>
<script setup lang="ts">
const props = defineProps({
    label: String,
    modelValue: String,
});
defineEmits(['update:modelValue']);
</script>
