<template>
  <h1>Configuration</h1>
  <form
    action="/api/admin/configuration"
    class="mt-10 flex flex-col gap-10"
    enctype="multipart/form-data"
    method="POST">
    <Input
      label="Password"
      name="password"
      required
      type="password" />
    <Input
      label="File"
      name="config_file"
      required
      type="file" />
    <button
      class="flex min-h-[50px] w-1/2 items-center justify-center bg-black text-center font-medium leading-4 text-white hover:bg-primary hover:text-black"
      type="submit">
      <span class="">Submit</span>
    </button>
  </form>
</template>

<script setup lang="ts">
import Input from '../components/base/Input.vue';
</script>
