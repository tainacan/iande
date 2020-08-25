import Vue from 'vue'

import AppointmentsAgenda from './components/admin/AppointmentsAgenda.vue'
import StatusMetabox from './components/admin/StatusMetabox.vue'

Vue.component('iande-appointments-agenda', AppointmentsAgenda)
Vue.component('iande-status-metabox', StatusMetabox)

jQuery(document).ready(() => {
    document.querySelectorAll('.iande-admin-app').forEach(el => {
        new Vue({ el })
    })
})