<!-- eslint-disable vue/no-v-html -->
<template>
  <h1 v-if="title" v-html="title.content" />
  <div v-if="description" v-html="description.content" />
  <h3 v-if="hintsTitle" class="mb-2 font-bold" v-html="hintsTitle.content" />
  <div v-if="hintsList" v-html="hintsList.content" />
</template>

<script setup lang="ts">
import type { IText } from '@/interfaces';

interface Props { texts: IText[] }
const props = defineProps<Props>(),

 title: IText | undefined = props.texts.find((text) => text.name === 'intro_title'),
 description: IText | undefined = props.texts.find((text) => text.name === 'intro_content'),
 hintsTitle: IText | undefined = props.texts.find((text) => text.name === 'hints_title'),
 hintsList: IText | undefined = props.texts.find((text) => text.name === 'hints_list');
</script>

<style>
ul {
  list-style: disc inside;
}
li {
  margin: 1px 0px;
}
</style>
