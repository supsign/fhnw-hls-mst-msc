<template>
    <div v-if="texts">
        <div class="mb-10">
            <div v-if="title" class="mb-2 text-2xl" v-html="title.content"></div>
            <div v-if="description" v-html="description.content"></div>
        </div>
        <div>
            <div v-if="hintsTitle" class="font-bold mb-2" v-html="hintsTitle.content"></div>
            <div v-if="hintsList" v-html="hintsList.content"></div>
        </div>
    </div>
</template>
<script setup lang="ts">
import type { PropType } from 'vue';
import type { IText } from '../../interfaces/text.interface';

const props = defineProps({
    texts: { type: Array as PropType<Array<IText>>, required: true },
});

const title: IText | null = props.texts.find((text) => text.name === 'intro_title') || null;
const description: IText | null = props.texts.find((text) => text.name === 'intro_content') || null;
const hintsTitle: IText | null = props.texts.find((text) => text.name === 'hints_title') || null;
const hintsList: IText | null = props.texts.find((text) => text.name === 'hints_list') || null;
</script>
<style>
ul {
    list-style: disc inside;
}
li {
    margin: 1px 0px;
}
</style>
