import Vue from 'vue'

import StatusMetabox from './components/StatusMetabox.vue'

Vue.component('iande-status-metabox', StatusMetabox)

jQuery(document).ready(() => {
    document.querySelectorAll('.iande-admin-app').forEach(el => {
        new Vue({ el })
    })
})