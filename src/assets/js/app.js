import Vue from 'vue'
import Vuelidate from 'vuelidate'
import Vuex from 'vuex'

import createStore from './store'

import './utils/icons'

// Lazy-loading candidates
import CreateAppointmentPage from './pages/create-appointment.vue'
import CreateUserPage from './pages/create-user.vue'
import EditAppointmentPage from './pages/edit-appointment.vue'
import ListAppointmentsPage from './pages/list-appointments.vue'
import LoginPage from './pages/login.vue'

Vue.use(Vuelidate)
Vue.use(Vuex)

Vue.component('iande-create-appointment-page', CreateAppointmentPage)
Vue.component('iande-create-user-page', CreateUserPage)
Vue.component('iande-edit-appointment-page', EditAppointmentPage)
Vue.component('iande-list-appointments-page', ListAppointmentsPage)
Vue.component('iande-login-page', LoginPage)

new Vue({
    el: '#iande-app',
    store: createStore()
})
