<script setup>
import { Dialog, DialogPanel, DialogTitle } from "@headlessui/vue";
import { TransitionChild, TransitionRoot } from "@headlessui/vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import DateTimePicker from "@/Components/DateTimePicker.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
  modelValue: String
})

const emit = defineEmits(['update:modelValue'])

function updateValue(value) {
  emit('update:modelValue', value)
}

const form = useForm({
    nume_eveniment: "",
    locatie: "",
    data_inceput: "",
    data_sfarsit: "",
});

const submit = () => {
    form.post(route("evenimente.store"), {
        onSuccess: () => {
            form.reset(
                "nume_eveniment",
                "locatie",
                "data_inceput",
                "data_sfarsit"
            );
            modelValue = false;
        },
        onError: () => {
            console.log("error");
        },
    });
};
</script>

<template>
            <TransitionRoot appear :show="isOpen" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black bg-opacity-25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md h-full max-h-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="text-lg font-medium leading-6 text-gray-900"
                                >
                                    Adauga Eveniment
                                </DialogTitle>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Adauga detaliile evenimentului.
                                    </p>
                                </div>
                                <div>
                                    <form @submit.prevent="submit">
                                        <div>
                                            <InputLabel
                                                for="nume_eveniment"
                                                value="Nume Eveniment"
                                            />

                                            <TextInput
                                                id="nume_eveniment"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.nume_eveniment"
                                                required
                                                autofocus
                                                autocomplete="nume_eveniment"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.nume_eveniment
                                                "
                                            />
                                        </div>

                                        <div class="mt-4">
                                            <InputLabel
                                                for="locatie"
                                                value="Locatie"
                                            />

                                            <TextInput
                                                id="locatie"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.locatie"
                                                required
                                                autocomplete="locatie"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.locatie"
                                            />
                                        </div>

                                        <div class="mt-4">
                                            <InputLabel
                                                for="password"
                                                value="Data si Ora Inceput"
                                            />

                                            <DateTimePicker
                                                v-model="form.data_inceput"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.data_inceput
                                                "
                                            />
                                        </div>

                                        <div class="mt-4">
                                            <InputLabel
                                                for="password_confirmation"
                                                value="Data si Ora Sfarsit"
                                            />

                                            <DateTimePicker
                                                v-model="form.data_sfarsit"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.data_sfarsit
                                                "
                                            />
                                        </div>

                                        <div
                                            class="flex items-center justify-end mt-4"
                                        >
                                            <PrimaryButton
                                                class="mt-4"
                                                :class="{
                                                    'opacity-25':
                                                        form.processing,
                                                }"
                                                :disabled="form.processing"
                                            >
                                                Salveaza
                                            </PrimaryButton>
                                        </div>
                                    </form>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
</template>
