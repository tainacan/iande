import '../scss/app.scss'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faAddressCard, faCalendar, faClock } from '@fortawesome/free-regular-svg-icons'
import { faAngleLeft, faAngleRight, faBars, faCheck, faMapMarkerAlt, faMinusCircle, faPencilAlt, faPlusCircle, faQuestionCircle, faSpinner, faTimes, faUniversity, faUser, faUsers } from '@fortawesome/free-solid-svg-icons'
import Vue from 'vue'
import Vuelidate from 'vuelidate'
import Vuex from 'vuex'

import Navbar from './components/Navbar.vue'
import createStore from './store'

library.add(faAddressCard, faCalendar, faClock)
library.add(faAngleLeft, faAngleRight, faBars, faCheck, faMapMarkerAlt, faMinusCircle, faPencilAlt, faPlusCircle, faQuestionCircle, faSpinner, faTimes, faUniversity, faUser, faUsers)

const ConfirmAppointmentPage = () => import(/* webpackChunkName: 'confirm-appointment-page' */ './pages/confirm-appointment.vue')
const CreateAppointmentPage = () => import(/* webpackChunkName: 'create-appointment-page' */ './pages/create-appointment.vue')
const CreateUserPage = () => import(/* webpackChunkName: 'create-user-page' */ './pages/create-user.vue')
const EditAppointmentPage = () => import(/* webpackChunkName: 'edit-appointment-page' */ './pages/edit-appointment.vue')
const EditInstitutionPage = () => import(/* webpackChunkName: 'edit-institution-page' */ './pages/edit-institution.vue')
const EditUserPage = () => import(/* webpackChunkName: 'edit-user-page' */ './pages/edit-user.vue')
const ListAppointmentsPage = () => import(/* webpackChunkName: 'list-appointments-page' */ './pages/list-appointments.vue')
const ListInstitutionsPage = () => import(/* webpackChunkName: 'list-institutions-page' */ './pages/list-institutions.vue')
const LoginPage = () => import(/* webpackChunkName: 'login-page' */ './pages/login.vue')

Vue.use(Vuelidate)
Vue.use(Vuex)

Vue.component('iande-confirm-appointment-page', ConfirmAppointmentPage)
Vue.component('iande-create-appointment-page', CreateAppointmentPage)
Vue.component('iande-create-user-page', CreateUserPage)
Vue.component('iande-edit-appointment-page', EditAppointmentPage)
Vue.component('iande-edit-institution-page', EditInstitutionPage)
Vue.component('iande-edit-user-page', EditUserPage)
Vue.component('iande-list-appointments-page', ListAppointmentsPage)
Vue.component('iande-list-institutions-page', ListInstitutionsPage)
Vue.component('iande-login-page', LoginPage)
Vue.component('iande-navbar', Navbar)

new Vue({
    el: '#iande-app',
    store: createStore()
})