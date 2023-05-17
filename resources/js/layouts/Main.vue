<template>
  <div class="flex min-h-screen w-full flex-col">
    <MainNav />
    <main class="relative grow">
      <div class="container mx-auto px-4">
        <router-view />
      </div>
    </main>
    <MainFooter />
  </div>
</template>

<script setup lang="ts">
import MainFooter from '../components/layout/MainFooter.vue';
import MainNav from '../components/layout/MainNav.vue';
</script>
