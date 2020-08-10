import Vue from 'vue'
import Vuelidate from 'vuelidate'
import Vuex from 'vuex'

import createStore from './store'

import './utils/icons'

// Lazy-loading candidates
import CreateUserPage from './pages/create-user.vue'
import LoginPage from './pages/login.vue'

Vue.use(Vuelidate)
Vue.use(Vuex)

Vue.component('app-create-user-page', CreateUserPage)
Vue.component('app-login-page', LoginPage)

new Vue({
    el: '#iande-app',
    store: createStore()
})
