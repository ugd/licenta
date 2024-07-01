<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { defineProps } from "vue";
const props = defineProps({
    telescopeUrl: String,
    frontendUrl: String,
    listmonkUrl: String,
});
const openTelescope = () => {
    window.open(props.telescopeUrl, "_blank");
};
const openFrontend = () => {
    window.open(props.frontendUrl, "_blank");
};
const openListmonks = () => {
    window.open(props.listmonkUrl, "_blank");
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
                <!-- Corectat: Utilizează :href pentru a lega telescopeUrl -->
            </div>
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <button
                                @click="openTelescope"
                                class="px-4 py-2 bg-violet-500 text-white font-bold rounded hover:bg-violet-700 transition"
                            >
                                Telescope
                            </button>
                            <button
                                @click="openFrontend"
                                class="ml-4 px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-700 transition"
                            >
                                Frontend
                            </button>
                            <button
                                @click="openListmonks"
                                class="ml-4 px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700 transition"
                            >
                                Listmonk
                            </button>
                        </div>
                    </div>
                    <!-- Corectat: Utilizează :href pentru a lega telescopeUrl -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
