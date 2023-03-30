<template>
  <h1>Configuration</h1>
  <form
    action="/api/admin/configuration"
    enctype="multipart/form-data"
    method="POST"
    class="mt-10 flex flex-col gap-10">
    <Input
      label="Password"
      name="password"
      type="password"
      required />
    <Input
      label="File"
      name="config_file"
      type="file"
      required />
    <button
      type="submit"
      class="flex min-h-[50px] w-1/2 items-center justify-center bg-black text-center font-medium leading-4 text-white hover:bg-primary hover:text-black">
      <span class="">Submit</span>
    </button>
  </form>
</template>

<script setup lang="ts">
import Input from '../components/base/Input.vue';
</script>
