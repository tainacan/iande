import '../scss/reports.scss'

import Vue from 'vue'
import VueApexCharts from 'vue-apexcharts'

import ReportsPage from '@components/ReportsPage.vue'
import WpI18n from '@plugins/wp-i18n'

Vue.use(VueApexCharts)
Vue.use(WpI18n)

Vue.components('ApexChart', VueApexCharts)
Vue.component('iande-reports-page', ReportsPage)

new Vue({
    el: '#iande-reports-app',
})
