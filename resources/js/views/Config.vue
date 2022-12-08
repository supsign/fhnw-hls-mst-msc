<template>
    <div class="container p-3 mx-auto">
        <Card>
            <form
                :action="env.VITE_API_URL + '/admin/configuration'"
                enctype="multipart/form-data"
                method="POST"
                class="flex flex-col gap-10"
            >
                <Input label="Password" name="password" type="password" required />
                <Input label="File" name="config_file" type="file" required />

                <button
                    type="submit"
                    class="transition transform duration-300 ease-in-out text-white py-1 px-4 rounded-md shadow-sm w-60 bg-blue-700 hover:bg-blue-800 hover:shadow-xl cursor-pointer"
                >
                    Submit
                </button>
            </form>
        </Card>
    </div>
</template>

<script setup lang="ts">
import Card from '../components/base/Card.vue';
import Input from '../components/base/Input.vue';
const env = import.meta.env;
</script>
