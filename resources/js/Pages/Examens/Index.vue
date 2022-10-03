<template>
    <AppLayout title="Examenes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Examenes
            </h2>
        </template>
        <div class="py-4 lg:px-8 md:px-6 sm:px-2">
            <div class="mx-auto 2xl:8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Fin Mensajes Flash -->
                    <!-- Encabezado y titulo -->
                    <section>
                        <div class="flex justify-between mx-auto p-4">
                            <div class="w-1/2">
                                <h1 class="font-bold text-xl text-black-800 leading-tight">
                                    Examenes
                                </h1>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="lg:px-4 md:px-2 sm:px-0 py-2 pb-6 overflow-y-auto h-40">
                            <table class="table-fixed w-full">
                                <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Id
                                    </th>
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Nombre
                                    </th>
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Cantidad de preguntas
                                    </th>
                                    <th class="px-4 py-2 text-sm font-bold w-2/12 hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Estado
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="text-center hover:bg-blue-100" v-on:dblclick="selectExamen(dato.id)" @click="selectPreguntas(dato.listapreguntas)" text-sm v-if="arrayData.data" v-for="(dato, id) in arrayData.data" :key="id">
                                    <td class="border px-1 py-2 text-sm truncate" v-text="dato.id"></td>
                                    <td class="border px-1 py-2 text-sm truncate" v-text="dato.nombre"></td>
                                    <td class="border px-1 py-2 text-sm truncate" v-text="dato.preguntas"></td>
                                    <td class="border px-2 py-2 text-sm truncate" v-if="dato.estado">
                                        <span class="inline-flex px-2 text-sm font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                            Activo
                                        </span>
                                    </td>
                                    <td class="border px-2 py-2 text-sm" v-else>
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                            Inactivo
                                        </span>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td class="border px-4 py-2 text-xs text-center" colspan="4"> La consulta no obtuvo datos</td>
                                </tr>
                                </tbody>
                            </table>
                            <section class="mt-6">
                                <div v-if="arrayData.links.length > 3">
                                    <div class="flex flex-wrap -mb-1">
                                        <template v-for="(link, p) in arrayData.links" :key="p">
                                            <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                                                 v-html="link.label" />
                                            <button  v-else
                                                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                                                     :class="{ 'bg-blue-700 text-white': link.active }"
                                                     v-on:click="cambiarPage(link.url)"
                                                     v-html="link.label" />
                                        </template>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </section>
                    <!-- Fin Tabla de contenido -->
                    <!-- Departamentos -->
                    <section>
                        <div class="flex justify-between mx-auto p-4">
                            <div class="w-1/2">
                                <h1 class="font-bold text-xl text-black-800 leading-tight">
                                    Preguntas
                                </h1>
                            </div>

                            <div class="w-1/2">
                                <div class="container flex justify-center items-center">
                                    <div class="relative">
                                        <div class="absolute top-4 left-3">
                                            <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i> </div>
                                        <input type="text" v-model="buscar" @keyup="getData2(buscar,'nombre')" class="h-8 w-96 pl-4 pr-4 rounded-lg z-0 focus:shadow focus:outline-none" placeholder="Buscar">
                                        <button @click="getData2(buscar,'nombre')">
                                            <div class="absolute top-2 right-2">
                                                <Icon icon="fe:search" class="h-4"  />
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Fin Encabezado y titulo -->
                    <!-- Tabla de contenido -->
                    <section>
                        <div class="lg:px-4 md:px-2 sm:px-0 py-2 pb-6 overflow-y-auto h-40">
                            <table class="table-fixed w-full">
                                <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Id
                                    </th>
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Nombre
                                    </th>
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Estado
                                    </th>
                                    <th class="px-4 py-2 text-sm font-bold w-2/12 hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Respuesta correcta
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="text-center hover:bg-blue-100" @click="selectOpciones(dato.opciones)" text-sm v-if="arrayData2.data" v-for="(dato, id) in arrayData2.data" :key="id">
                                    <td class="border px-1 py-2 text-sm truncate" v-text="dato.id"></td>
                                    <td class="border px-2 py-2 text-sm truncate" v-text="dato.nombre"></td>
                                    <td class="border px-2 py-2 text-sm truncate" v-if="dato.estado">
                                        <span class="inline-flex px-2 text-sm font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                            Activo
                                        </span>
                                    </td>
                                    <td class="border px-2 py-2 text-sm" v-else>
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                            Inactivo
                                        </span>
                                    </td>
                                    <td class="border px-2 py-2 text-sm truncate" v-text="dato.respuesta.nombre"></td>
                                </tr>
                                <tr v-else>
                                    <td class="border px-4 py-2 text-xs text-center" colspan="3"> La consulta no obtuvo datos</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </section>
                    <!-- Fin Departamentos -->


                    <!-- Ciudades -->
                    <section>
                        <div class="flex justify-between mx-auto p-4">
                            <div class="w-1/2">
                                <h1 class="font-bold text-xl text-black-800 leading-tight">
                                    Opciones
                                </h1>
                            </div>

                            <div class="w-1/2">
                                <div class="container flex justify-center items-center">
                                    <div class="relative">
                                        <div class="absolute top-4 left-3">
                                            <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i> </div>
                                        <input type="text" v-model="buscar" @keyup="getData3(buscar,'nombre')" class="h-8 w-96 pl-4 pr-4 rounded-lg z-0 focus:shadow focus:outline-none" placeholder="Buscar">
                                        <button @click="getData3(buscar,'nombre')">
                                            <div class="absolute top-2 right-2">
                                                <Icon icon="fe:search" class="h-4"  />
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Fin Encabezado y titulo -->
                    <!-- Tabla de contenido -->
                    <section>
                        <div class="lg:px-4 md:px-2 sm:px-0 py-2 pb-6 overflow-y-auto h-40">
                            <table class="table-fixed w-full">
                                <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Id
                                    </th>
                                    <th class="px-4 py-2 w-2/12 text-sm font-bold hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Nombre
                                    </th>
                                    <th class="px-4 py-2 text-sm font-bold w-2/12 hover:bg-blue-500 hover:text-gray-50 rounded-b">
                                        Estado
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="text-center" text-sm v-if="arrayData3.data" v-for="(dato, id) in arrayData3.data" :key="id">
                                    <td class="border px-1 py-2 text-sm truncate" v-text="dato.id"></td>
                                    <td class="border px-2 py-2 text-sm truncate" v-text="dato.nombre"></td>
                                    <td class="border px-2 py-2 text-sm truncate" v-if="dato.estado">
                                        <span class="inline-flex px-2 text-sm font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                            Activo
                                        </span>
                                    </td>
                                    <td class="border px-2 py-2 text-sm" v-else>
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                            Inactivo
                                        </span>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td class="border px-4 py-2 text-xs text-center" colspan="3"> La consulta no obtuvo datos</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </section>
                    <!-- Fin Ciudades -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from "sweetalert2";
import { Icon } from '@iconify/vue';
import Pagination from '@/Components/Pagination';

import Toggle from '@vueform/toggle';
import '@vueform/toggle/themes/default.css';
import Button from "../../Jetstream/Button";

import moment from 'moment'
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { ref, onMounted } from 'vue';
import { Money3Component } from 'v-money3'

import { Inertia } from '@inertiajs/inertia';
import { Head, Link } from '@inertiajs/inertia-vue3';
import JetNavLink from '@/Jetstream/NavLink.vue';
import NavLink from "../../Jetstream/NavLink";


export default {

    components: {
        Button,
        AppLayout,
        Icon,
        Pagination,
        Toggle,
        QuillEditor,
        money3: Money3Component,
        Link
    },
    props:{
        examenes: [],
        errors: Object,
    },
    computed: {
    },
    data() {
        return {
            configMoney: {
                masked: false,
                prefix: '$ ',
                suffix: '',
                thousands: ',',
                decimal: '.',
                precision: 0,
                disableNegative: false,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
            },
            ispage: true,
            tituloModal: '',
            arrayData: {
                data: [],
                links: []
            },
            arrayData2: {
                data: [],
                links: []
            },
            arrayData3: {
                data: [],
                links: []
            },
            editMode: false,
            verMode: false,
            isOpen: false,
            buscar: '',
            sortOrder: 1,
            sortBy: '',
            existedata: 0,
            existedata2: 0,
            existedata3: 0,
        }
    },
    methods: {
        selectPreguntas: function (data) {
            this.arrayData2.data = data;
        },
        selectOpciones: function (data) {
            this.arrayData3.data = data;

            if (this.arrayData3.data.length > 0) {
                this.existedata3 = 1;
            } else {
                this.existedata3 = 0;
            }
        },
        selectExamen: function (data) {
            this.$inertia.get('/examens/' + data);
        },
    },
    created: function () {
        this.arrayData = this.examenes;
    },
    mounted() {
    },
}
</script>
