import Vue from 'vue'

// Lazy-loading candidates
import LoginPage from './pages/login.vue'

Vue.component('app-login-page', LoginPage)

new Vue().$mount('#iande-app')
