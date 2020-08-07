import Vue from 'vue'
import Vuelidate from 'vuelidate'

// Lazy-loading candidates
import LoginPage from './pages/login.vue'

Vue.use(Vuelidate)

Vue.component('app-login-page', LoginPage)

new Vue().$mount('#iande-app')
