<template>
    <div class="flex flex-col gap-5">
        <div class="text-lg font-bold">Additional Comments on the Module Choice</div>
        <textarea
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
            class="block py-2 px-4 w-full box-border border rounded-lg border-gray-200 shadow-md text-gray-900"
        />
    </div>
</template>
<script setup lang="ts">
const props = defineProps({
    modelValue: String,
});
const emits = defineEmits(['update:modelValue']);
</script>
