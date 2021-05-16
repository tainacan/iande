import '../scss/app.scss'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faAddressCard, faCalendar, faClock, faImage } from '@fortawesome/free-regular-svg-icons'
import { faAngleLeft, faAngleRight, faBars, faCaretDown, faCheck, faList, faMapMarkerAlt, faMinus, faMinusCircle, faPencilAlt, faPlusCircle, faQuestionCircle, faSpinner, faTimes, faUniversity, faUser, faUsers } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Vue from 'vue'
import VueAsyncComputed from 'vue-async-computed'
import Vuelidate from 'vuelidate'
import Vuex from 'vuex'

import Navbar from '@components/Navbar.vue'
import Page from '@components/Page.vue'
import IandePlugin from '@plugins/iande'
import WpI18n from '@plugins/wp-i18n'
import createStore from '@store'

library.add(faAddressCard, faCalendar, faClock, faImage)
library.add(faAngleLeft, faAngleRight, faBars, faCaretDown, faCheck, faList, faMapMarkerAlt, faMinus, faMinusCircle, faPencilAlt, faPlusCircle, faQuestionCircle, faSpinner, faTimes, faUniversity, faUser, faUsers)

const LoginPage = () => import(/* webpackChunkName: 'login-page' */ '@pages/login.vue')

Vue.use(IandePlugin)
Vue.use(VueAsyncComputed)
Vue.use(Vuelidate)
Vue.use(Vuex)
Vue.use(WpI18n)

Vue.component('iande-login-page', LoginPage)
Vue.component('iande-navbar', Navbar)
Vue.component('iande-page', Page)
Vue.component('Icon', FontAwesomeIcon)

new Vue({
    el: '#iande-app',
    store: createStore()
})
