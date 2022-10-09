<template>
    <AppLayout title="Examen">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Examen
            </h2>
        </template>
        <div class="py-4 lg:px-4 md:px-2 sm:px-2">
            <div class="mx-auto 2xl:8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Mensajes Flash -->
                    <section>
                        <div @click="cleanMessage()" mx-auto class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-show="$page.props.flash.message">
                            <div class="flex">
                                <div>
                                    <p class="text-sm">{{ $page.props.flash.message }}</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Fin Mensajes Flash -->
                    <!-- Encabezado y titulo -->
                    <section>
                        <div class="flex justify-between mx-auto p-4">
                            <div class="w-full">
                                <h1 class="font-bold text-xl text-black-800 leading-tight">
                                    {{ examen.nombre }}
                                </h1>
                            </div>
                        </div>
                    </section>
                    <!-- Fin Encabezado y titulo -->

                    <section>
                        <div class="px-4">
                            <form @submit.prevent="getUsers('', '', form)" @keyup.enter="getUsers('', '', form)">
                                <div class="grid xl:grid-cols-2 xl:gap-6">
                                    <div class="relative z-0 w-full mb-6 group">
                                        <button @click="openModal('registrar')" class="bg-blue-500 text-xs  hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ">Ver curso</button>
                                    </div>

                                    <div class="relative z-0 w-full mb-6 group">
                                        El curso tiene una duración de 40 minutos y lo podrá repetir las veces que se desee.
                                    </div>
                                    <div class="relative z-0 w-full mb-6 group">
                                        El examen de certificación tendrá una duración de 20 minútos y para poder aprobarlo deberá superar 70% de respuestas correctas.
                                    </div>

                                    <div v-if="!(examenuser?(examenuser.estado==1)?true:false:false)" class="relative z-0 w-full mb-6 group">
                                        <button @click="openModal('examen')" class="bg-red-500 text-xs  hover:bg-red-700 text-white font-bold py-2 px-4 rounded ">Presentar examen</button>
                                    </div>
                                    <div class="relative z-0 w-full mb-6 group">
                                        Estado examen: <span class="font-bold">{{ examenuser?(examenuser.estado==1?'Aprobado':'No aprobado'):'No presentado' }}</span>
                                    </div>
                                    <div class="relative z-0 w-full mb-6 group">
                                        Cantidad de preguntas:  <span class="font-bold">{{ examen.preguntas }}</span>
                                    </div>
                                    <div class="relative z-0 w-full mb-6 group">
                                        Calificación:  <span class="font-bold">{{ examenuser?examenuser.calificacion:'0' }} ( {{examenuser?examenuser.respuestas:'0' }} de {{ examen.preguntas }})</span>
                                    </div>
                                    <div class="relative z-0 w-full mb-6 group">
                                        Fecha de presentación de examen: <span class="font-bold">{{ examenuser?dateTimeFull(examenuser.updated_at):'' }}</span>
                                    </div>

                                    <div v-if="(examenuser?(examenuser.estado==1)?true:false:false)" class="relative z-0 w-full mb-6 group">
                                        <button @click="openModal('registrar')" :disabled="!(examenuser?examenuser.estado:false)" class="bg-yellow-500 text-xs  hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded ">Descargar certificado</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Ventana modal Detalles -->
                    <section> <!-- Ventana modal -->
                        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                                <div class="fixed inset-0 transition-opacity">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <!-- This element is to trick the browser into centering the modal contents. -->
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                                <div class="inline-block lg:w-8/12 align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                                    <button type="button" @click="closeModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="authentication-modal">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                    <div class="flex">
                                        <h2 v-text="tituloModal" class="w-1/2 text-sm font-bold text-gray-900 px-4 py-4"></h2>
                                        <vue-countdown ref="countdown" class="p-2 w-1/2 rounded-md mx-auto text-blue-700" :time="time" v-slot="{ minutes, seconds }" @end="onCountdownEnd">
                                            Tiempo restante：{{ minutes }} min, {{ seconds }} seg.
                                        </vue-countdown>
                                    </div>

                                    <form method="get" action="/examens/putExamen">
                                        <div class="bg-white px-4 pt-2 pb-4 sm:p-6 sm:pb-4">
                                            <div class="">
                                                <section>
                                                    <div v-for="(dato, id) in arrayPreguntas" :key="id" class="mt-2 grid grid-cols-1 gap-y-6 sm:gap-x-4">
                                                        <div>
                                                            <h3 class="mb-4 font-semibold text-gray-900">{{ id + 1 }}. {{ dato.nombre }}</h3>
                                                            <ul class="w-100 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200">
                                                                <li v-for="(opcion, id) in dato.opciones" :key="id" class="w-full rounded-t-lg border-b border-gray-200">
                                                                    <div class="flex items-center pl-3">
                                                                        <input id="list-radio-license" type="radio" :value="opcion.id" :name="dato.id" :id="dato.id" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                                                        <label for="list-radio-license" class="py-3 ml-2 w-full text-sm font-medium text-gray-900">{{ opcion.nombre }}</label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                              <button wire:click.prevent="submit()" type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5" >
                                                Terminar
                                              </button>
                                            </span>

                                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                          <button @click="closeModal()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                            Cancelar
                                          </button>
                                        </span>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Fin Ventana modal Detalles -->

                    <!-- Ventana modal cambiar passwd -->
                    <!-- Main modal -->
                    <section> <!-- Ventana modal -->
                        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpencambiopass">
                            <div class="items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                                <div class="fixed inset-0 transition-opacity">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>

                                <!-- This element is to trick the browser into centering the modal contents. -->
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                                <div class="inline-block lg:w-6/12 align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                                    <button type="button" @click="closeModalPass()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="authentication-modal">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                    <div class="">
                                        <h2 v-text="tituloModal" class="text-xl font-bold text-gray-900 px-4 py-4"></h2>
                                    </div>
                                    <form @submit.prevent="submit">
                                        <div class="bg-white px-4 pt-2 pb-4 ">
                                            <div class="">
                                                <section>
                                                    <div class="mt-2 grid gap-y-6 ">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Password</label>
                                                            <div class="mt-1">
                                                                <input type="password" v-model="formpasswd.password" autocomplete="given-name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                                <div v-if="$page.props.errors.updatePassword" class="text-red-500">{{ $page.props.errors.updatePassword }}</div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Confirmar Password</label>
                                                            <div class="mt-1">
                                                                <input type="password" v-model="formpasswd.password_confirmation" autocomplete="given-name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                              <button wire:click.prevent="updatePass()" @click="updatePass(formpasswd)" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5" >
                                                Cambiar password
                                              </button>
                                            </span>
                                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">

                                          <button @click="closeModalPass()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                            Cancelar
                                          </button>
                                        </span>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Fin Ventana modal passwd -->

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from "sweetalert2";
import { Icon } from '@iconify/vue';
import Toggle from '@vueform/toggle';
import '@vueform/toggle/themes/default.css';
import Button from "../../Jetstream/Button";
import moment from 'moment'
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { Money3Component } from 'v-money3'
import {Head, Link, usePage} from '@inertiajs/inertia-vue3';
import JetNavLink from '@/Jetstream/NavLink.vue';
import NavLink from "../../Jetstream/NavLink";
import Input from "../../Jetstream/Input";
import VueCountdown from "@chenfengyuan/vue-countdown";

export default {

    components: {
        Input,
        NavLink,
        Button,
        AppLayout,
        Icon,
        Toggle,
        QuillEditor,
        JetNavLink,
        Link,
        money3: Money3Component,
        VueCountdown

    },
    props:{
        examen : null,
        user: null,
        examenuser: null,
        arrayPreguntas: [],
        errors: Object
    },
    computed: {

    },
    data() {
        return {
            time: 2 * 60 * 1000,
            tituloModal: '',
            formpasswd: {
                _token: usePage().props.value._token,
                id: '',
                password: '',
                tipouser: 'cliente',
                password_confirmation: '',
            },
            form: {
                id: null,
                nombre: '',
                email: null,
                username: null,
                apellido: null,
                idrol: 2,
                estado: 1,
                idtipos_documento: 0,
                documento: null,
                direccion: null,
                indicativo: 0,
                iddepartamento: 0,
                idciudad: 0,
                idpais: 0,
                observaciones: null,
                movil: null,
                isnatural: 0,
                camaracomercio: false,
                rut: false,
                url: false,
                cambiarpassword: true
            },
        }
    },
    methods: {
        cambiarPass: function(){
            this.isOpencambiopass = true;
        } ,
        updatePass: function(data) {
            this.$inertia.post('/changepasssu', data, {
                onSuccess: (page) => {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Se ha cambiado la contraseña',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    this.formpasswd.id = 0;
                    this.formpasswd.password = '';
                    this.formpasswd.password_confirmation = '';
                    this.isOpencambiopass = false;
                    this.getUsers('','nombre');
                    this.editMode = false;
                    this.closeModal();
                },
            });
        },
        openModal: function (accion, data = []) {
            this.isOpen = true;

            switch (accion) {
                case 'examen':
                {
                    this.tituloModal = 'Examen de certificación';
                    break;
                }
                case 'ver': {
                    this.tituloModal = 'Ver Usuario ' + data['username'];
                    this.form.idpais = data['idpais'];
                    this.form.iddepartamento = data['iddepartamento'];
                    this.form.idciudad = data['idciudad'];
                    this.form.idtipos_documento = data['idtipos_documento'];
                    this.form.idrol = data['idrol'];
                    this.form.idempresa = data['idempresa'];
                    this.form.nombre = data['nombre'];
                    this.form.apellido = data['apellido'];
                    this.form.email = data['email'];
                    this.form.movil = data['movil'];
                    this.form.documento = data['documento'];
                    this.form.username = data['username'];
                    this.form.direccion = data['direccion'];
                    this.form.telefono = data['telefono'];
                    this.getRoles();
                    this.getPaises();
                    this.getCiudades();
                    this.getDepartamentos();
                    this.getTiposdocumento();
                    this.getEmpresas();
                    this.newMode = false;
                    this.verMode = true;
                    this.editMode = false;
                    break;
                    break;
                }
                case 'actualizar': {
                    this.form.id = data['id'];
                    this.tituloModal = 'Actualizar Usuario ' + data['username'];
                    this.form.idpais = data['idpais'];
                    this.form.iddepartamento = data['iddepartamento'];
                    this.form.idciudad = data['idciudad'];
                    this.form.idtipos_documento = data['idtipos_documento'];
                    this.form.idrol = data['idrol'];
                    this.form.idempresa = data['idempresa'];
                    this.form.nombre = data['nombre'];
                    this.form.apellido = data['apellido'];
                    this.form.email = data['email'];
                    this.form.movil = data['movil'];
                    this.form.documento = data['documento'];
                    this.form.username = data['username'];
                    this.form.direccion = data['direccion'];
                    this.form.telefono = data['telefono'];
                    this.getRoles();
                    this.getPaises();
                    this.getCiudades();
                    this.getDepartamentos();
                    this.getTiposdocumento();
                    this.getEmpresas();
                    this.newMode = false;
                    this.verMode = false;
                    this.editMode = true;
                    break;
                    break;
                }

            }
        },
        closeModal: function () {
            this.isOpen = false;
            this.reset();
            this.editMode = false;
            this.verMode  = false;
            this.$page.props.errors = [];
        },
        closeModalPass: function () {
            this.isOpencambiopass = false
            this.$page.props.errors.updatePassword = null;
        },
        reset: function () {
            this.tituloModal = 'Crear nuevo rifa de venta';
            this.form.id = null;
            this.form.nombre = null;
            this.form.documento = null;
            this.form.apellido = null;
            this.form.email = null;
            this.form.telefono = null;
            this.form.movil = null;
            this.form.username = null;
            this.form.password = null;
            this.form.idpais = 0;
            this.form.iddepartamento = 0;
            this.form.idciudad = 0;
            this.form.direccion = null;
            this.form.idrol = 0;
            this.form.idempresa = null;
            this.form.fechafin = null;
        },
        save: function (data) {
            this.$inertia.post('/users', data, {
                onSuccess: (page) => {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'El cliente se ha creado',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    this.reset();
                    this.closeModal();
                    this.getUsers('','nombre');
                    this.editMode = false;
                },
            });

        },
        edit: function (data) {
            //this.form = Object.assign({}, data);
            this.editMode = true;
            //console.log(this.form);
            this.formpasswd.id = data['id'];
            this.openModal('actualizar', data);
        },
        ver: function (data) {
            this.verMode = true;
            this.openModal('ver', data);
        },
        update: function (data) {
            data._method = 'PUT';
            this.$inertia.post('/users/cliente/' + data.id, data, {
                onSuccess: (page) => {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'El cliente se ha actualizado!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    this.getUsers('','nombre');
                    this.closeModal();
                    this.reset();
                    this.editMode = false;
                    this.verMode = false;
                    this.newMode = false;
                },
            });
        },
        getUsers: function (buscar, sortBy, filtros = []) {
            this.buscar = buscar;

            if (sortBy == this.sortBy){
                this.sortOrder = !this.sortOrder;
            }
            let sortOrderdesc;
            if (this.sortOrder){
                sortOrderdesc = 'asc';
            } else {
                sortOrderdesc = 'desc';
            }
            this.sortBy = sortBy;
            this.ispage = true;

            var url= '/users/indexclientes';
            axios.get(url, {
                params: {
                    filtros: filtros,
                    buscar: this.buscar,
                    sortBy: this.sortBy,
                    sortOrder: sortOrderdesc,
                    ispage: this.ispage
                }
            }).then((res) => {
                var respuesta = res.data;
                this.arrayData = respuesta.clientes;

            })
        },
        UsersExport: function (filtros = []) {
            let fecha = moment(new Date()).format('DDMMYYYY');
            var url= '/clientes/export';
            axios.get(url, {
                params: {
                    filtros: filtros,
                },
                responseType: 'blob',
            }).then((response) => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');

                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'clientes_'+ fecha + '.xlsx');
                document.body.appendChild(fileLink);

                fileLink.click();
            })
        },
    },
    created: function () {
        this.arrayData = this.clientes;
    },
    mounted() {
    },
}
</script>
