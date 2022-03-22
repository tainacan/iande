__webpack_public_path__ = window.IandeSettings.iandePath

import { library } from '@fortawesome/fontawesome-svg-core'
import { faFacebook, faTelegram, faTwitter, faWhatsapp, faWordpressSimple } from '@fortawesome/free-brands-svg-icons'
import { faAddressCard, faCalendar, faClock, faFloppyDisk, faImage, faStar as farStar, faTrashCan } from '@fortawesome/free-regular-svg-icons'
import { faAngleDoubleLeft, faAngleDoubleRight, faAngleLeft, faAngleRight, faArrowLeft, faBars, faBus, faCaretDown, faCircleCheck, faCircleInfo, faCircleMinus, faCirclePlus, faCircleQuestion, faCircleXmark, faEye, faGear, faGripVertical, faLandmark, faListUl, faPen, faPrint, faShareNodes, faSpinner, faStar, faUserLarge, faUsers, faXmark } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Vue from 'vue'
import VueAsyncComputed from 'vue-async-computed'
import Vuelidate from 'vuelidate'
import Vuex from 'vuex'

import Navbar from '@components/Navbar.vue'
import PageLoader from '@components/PageLoader.vue'
import IandePlugin from '@plugins/iande'
import WpI18n from '@plugins/wp-i18n'
import createStore from '@store'

library.add(faFacebook, faTelegram, faTwitter, faWhatsapp, faWordpressSimple)
library.add(faAddressCard, faCalendar, faClock, faFloppyDisk, faImage, farStar, faTrashCan)
library.add(faAngleDoubleLeft, faAngleDoubleRight, faAngleLeft, faAngleRight, faArrowLeft, faBars, faBus, faCaretDown, faCircleCheck, faCircleInfo, faCircleMinus, faCirclePlus, faCircleQuestion, faCircleXmark, faEye, faGear, faGripVertical, faLandmark, faListUl, faPen, faPrint, faShareNodes, faSpinner, faStar, faUserLarge, faUsers, faXmark)

const EditItineraryPage = () => import(/* webpackChunkName: 'edit-itinerary-page' */ '@pages/edit-itinerary.vue')
const LoginPage = () => import(/* webpackChunkName: 'login-page' */ '@pages/login.vue')

Vue.use(IandePlugin)
Vue.use(VueAsyncComputed)
Vue.use(Vuelidate)
Vue.use(Vuex)
Vue.use(WpI18n)

Vue.component('iande-edit-itinerary-page', EditItineraryPage)
Vue.component('iande-login-page', LoginPage)
Vue.component('iande-navbar', Navbar)
Vue.component('iande-page-loader', PageLoader)
Vue.component('Icon', FontAwesomeIcon)

new Vue({
    el: '#iande-app',
    store: createStore(),
})
