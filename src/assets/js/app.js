import '../scss/app.scss'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faAddressCard, faCalendar, faClock, faImage, faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { faAngleLeft, faAngleRight, faBars, faCaretDown, faCheck, faCheckCircle, faGripVertical, faList, faMapMarkerAlt, faMinus, faMinusCircle, faPencilAlt, faPlusCircle, faQuestionCircle, faSpinner, faTimes, faUniversity, faUser, faUsers } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Vue from 'vue'
import VueAsyncComputed from 'vue-async-computed'
import Vuelidate from 'vuelidate'
import Vuex from 'vuex'

import Navbar from '@components/Navbar.vue'
import IandePlugin from '@plugins/iande'
import WpI18n from '@plugins/wp-i18n'
import createStore from '@store'

library.add(faAddressCard, faCalendar, faClock, faImage, faTrashAlt)
library.add(faAngleLeft, faAngleRight, faBars, faCaretDown, faCheck, faCheckCircle, faGripVertical, faList, faMapMarkerAlt, faMinus, faMinusCircle, faPencilAlt, faPlusCircle, faQuestionCircle, faSpinner, faTimes, faUniversity, faUser, faUsers)

const ChangePasswordPage = () => import(/* webpackChunkName: 'change-password-page' */ '@pages/change-password.vue')
const CheckinPage = () => import(/* webpackChunkName: 'checkin-page' */ '@pages/checkin.vue')
const ConfirmAppointmentPage = () => import(/* webpackChunkName: 'confirm-appointment-page' */ '@pages/confirm-appointment.vue')
const CreateAppointmentPage = () => import(/* webpackChunkName: 'create-appointment-page' */ '@pages/create-appointment.vue')
const CreateItineraryPage = () => import(/* webpackChunkName: 'create-itinerary-page' */ '@pages/create-itinerary.vue')
const CreateUserPage = () => import(/* webpackChunkName: 'create-user-page' */ '@pages/create-user.vue')
const EditAppointmentPage = () => import(/* webpackChunkName: 'edit-appointment-page' */ '@pages/edit-appointment.vue')
const EditInstitutionPage = () => import(/* webpackChunkName: 'edit-institution-page' */ '@pages/edit-institution.vue')
const EditItineraryPage = () => import(/* webpackChunkName: 'edit-itinerary-page' */ '@pages/edit-itinerary.vue')
const EditUserPage = () => import(/* webpackChunkName: 'edit-user-page' */ '@pages/edit-user.vue')
const EducatorReportPage = () => import(/* webpackChunkName: 'educator-report-page' */ '@pages/report.vue')
const FeedbackPage = () => import(/* webpackChunkName: 'feedback-page' */ '@pages/feedback.vue')
const GroupsAgendaPage = () => import(/* webpackChunkName: 'groups-agenda-page' */ '@pages/agenda.vue')
const ListAppointmentsPage = () => import(/* webpackChunkName: 'list-appointments-page' */ '@pages/list-appointments.vue')
const ListGroupsPage = () => import(/* webpackChunkName: 'list-groups-page' */ '@pages/list-groups.vue')
const ListInstitutionsPage = () => import(/* webpackChunkName: 'list-institutions-page' */ '@pages/list-institutions.vue')
const LoginPage = () => import(/* webpackChunkName: 'login-page' */ '@pages/login.vue')
const WelcomePage = () => import(/* webpackChunkName: 'welcome-page' */ '@pages/welcome.vue')

Vue.use(IandePlugin)
Vue.use(VueAsyncComputed)
Vue.use(Vuelidate)
Vue.use(Vuex)
Vue.use(WpI18n)

Vue.component('iande-change-password-page', ChangePasswordPage)
Vue.component('iande-checkin-page', CheckinPage)
Vue.component('iande-confirm-appointment-page', ConfirmAppointmentPage)
Vue.component('iande-create-appointment-page', CreateAppointmentPage)
Vue.component('iande-create-itinerary-page', CreateItineraryPage)
Vue.component('iande-create-user-page', CreateUserPage)
Vue.component('iande-edit-appointment-page', EditAppointmentPage)
Vue.component('iande-edit-institution-page', EditInstitutionPage)
Vue.component('iande-edit-itinerary-page', EditItineraryPage)
Vue.component('iande-edit-user-page', EditUserPage)
Vue.component('iande-educator-report-page', EducatorReportPage)
Vue.component('iande-feedback-page', FeedbackPage)
Vue.component('iande-groups-agenda-page', GroupsAgendaPage)
Vue.component('iande-list-appointments-page', ListAppointmentsPage)
Vue.component('iande-list-groups-page', ListGroupsPage)
Vue.component('iande-list-institutions-page', ListInstitutionsPage)
Vue.component('iande-login-page', LoginPage)
Vue.component('iande-navbar', Navbar)
Vue.component('iande-welcome-page', WelcomePage)
Vue.component('Icon', FontAwesomeIcon)

new Vue({
    el: '#iande-app',
    store: createStore(),
})
