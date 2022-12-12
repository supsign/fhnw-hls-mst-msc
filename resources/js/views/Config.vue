<template>
    <div class="container mx-auto p-3">
        <Card>
            <form
                :action="'/api/admin/configuration'"
                enctype="multipart/form-data"
                method="POST"
                class="flex flex-col gap-10"
            >
                <Input label="Password" name="password" type="password" required />
                <Input label="File" name="config_file" type="file" required />

                <button
                    type="submit"
                    class="rounded-md cursor-pointer bg-blue-700 shadow-sm text-white py-1 transform px-4 transition ease-in-out w-60 duration-300 hover:bg-blue-800 hover:shadow-xl"
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

</script>
