<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetCheckbox from '@/Jetstream/Checkbox.vue';
import JetLabel from '@/Jetstream/Label.vue';
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue';
import { usePage } from '@inertiajs/inertia-vue3'

defineProps({
    //canResetPassword: Boolean,
    status: String,
    _token: String
});

const form = useForm({
    code: '',
    _token: usePage().props.value._token,
});

const submit = () => {
    form.transform(data => ({
        ...data,
    })).post(route('2fa.post'), {
        onFinish: () => form.reset('code'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <JetValidationErrors class="mb-4" />

        <div mx-auto class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-show="$page.props.flash.message">
            <div class="flex">
                <div>
                    <p class="text-sm">{{ $page.props.flash.message }}</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="code" value="Ingrese el código enviado al teléfono " />
                <JetInput
                    id="code"
                    v-model="form.code"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('2fa.resend')" class="underline text-sm text-gray-600 hover:text-gray-900 mx-4">
                    Reenviar
                </Link>
                <Link :href="route('2fa.sendemail')" class="underline text-sm text-gray-600 hover:text-gray-900 mx-4">
                    Email
                </Link>

                <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Ingresar
                </JetButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
