<template>
    <div class="bg-hls border shadow-xl p-2 top-0 z-10 sticky self-start">
        <div v-if="description" v-html="description.content"></div>
        <div class="divide-black flex font-bold divide-x-2 gap-5">
            <div>Current ECTS: {{ statistics.ects }}/50</div>
            <div class="pl-5">{{ statistics.specialization }} S</div>
            <div class="pl-5">{{ statistics.cluster }} CS</div>
            <div class="pl-5">{{ statistics.core }} CC</div>
            <div class="pl-5">{{ statistics.outside }} Modules Outside</div>
        </div>
    </div>
</template>
<script setup lang="ts">
import type { PropType } from 'vue';
import type { IStatistics } from '../../interfaces/statistics.interface';
import type { IText } from '../../interfaces/text.interface';

const props = defineProps({
    texts: { type: Array as PropType<Array<IText>>, required: true },
    statistics: { type: Object as PropType<IStatistics>, required: true },
});
const description: IText | null = props.texts.find((text) => text.name === 'description_before_further') || null;
</script>
