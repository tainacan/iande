import Vue from 'vue'

import AppointmentsAgenda from './components/admin/AppointmentsAgenda.vue'
import StatusMetabox from './components/admin/StatusMetabox.vue'
import { api } from './utils'
import { cep } from './utils/validators'

// Lazy-loading candidates
Vue.component('iande-appointments-agenda', AppointmentsAgenda)
Vue.component('iande-status-metabox', StatusMetabox)

jQuery(document).ready(() => {
    document.querySelectorAll('.iande-admin-app').forEach(el => {
        new Vue({ el })
    })

    jQuery('input#zip_code').change(async (e) => {
        const zipCode = e.target.value
        if (zipCode && cep(zipCode)) {
            try {
                const res = await api.get(`https://viacep.com.br/ws/${zipCode}/json/`)
                if (!res.erro) {
                    jQuery('input#address').val(res.logradouro || '')
                    jQuery('input#address_number').val('')
                    jQuery('input#city').val(`${res.uf}${res.ibge.slice(2)}`)
                    jQuery('input#complement').val(res.complemento || '')
                    jQuery('input#district').val(res.bairro || '')
                    jQuery('input#state').val(res.uf)
                }
            } catch (err) {
                console.error(err)
            }
        }
    })
})

console.log('test')