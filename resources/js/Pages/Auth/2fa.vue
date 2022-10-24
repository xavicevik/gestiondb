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
    code1: null,
    code2: null,
    code3: null,
    code4: null,
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


<script>

export default {


    data() {
        return {
            verHistorialobs: null,
            activetab: '1',
        }
    },
    methods: {
        finish: function() {
            //this.form.code = document.getElementById('pincode-1').value + document.getElementById('pincode-2').value + document.getElementById('pincode-3').value + document.getElementById('pincode-4').value;
            this.form.code = this.form.code1 + this.form.code2 + this.form.code3 + this.form.code4;
            console.log(this.form);
        },
        next: function(next) {
            document.getElementById(next).focus();
        },
    },
}
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

        <form @submit.prevent="submit" autocomplete="off">
            <div>
                <JetLabel for="code" value="Ingrese el código enviado al teléfono o al correo" />
                <div class="mx-auto items-center justify-center w-full">
                    <div class="flex items-center justify-center py-2">
                        <input @keyup="next('code2')" class="text-center border-b-2 border-t-0 border-l-0 border-r-0 p-2 w-10" id="code1" type="tel" v-model="form.code1" maxlength="1" placeholder="·" autocomplete="off">
                        <input @keyup="next('code3')" class="text-center border-b-2 border-t-0 border-l-0 border-r-0 p-2 w-10" id="code2" type="tel" v-model="form.code2" maxlength="1" placeholder="·" autocomplete="off">
                        <input @keyup="next('code4')" class="text-center border-b-2 border-t-0 border-l-0 border-r-0 p-2 w-10" id="code3" type="tel" v-model="form.code3" maxlength="1" placeholder="·" autocomplete="off">
                        <input @keyup="finish()" class="text-center border-b-2 border-t-0 border-l-0 border-r-0 p-2 w-10" id="code4" type="tel" v-model="form.code4" maxlength="1" placeholder="·" autocomplete="off">
                        <input type="text" v-model="form.code" hidden>
                    </div>
                </div>

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
