import Vue from 'vue'

import StatusMetabox from './components/StatusMetabox.vue'

Vue.component('iande-status-metabox', StatusMetabox)

jQuery(document).ready(() => {
    
    new Vue({
        el: document.querySelector('#iande-admin-app'),
    })
    
})