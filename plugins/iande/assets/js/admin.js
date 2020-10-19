import '../scss/admin.scss'

import Vue from 'vue'

import { api } from './utils'
import { cep } from './utils/validators'

const AppointmentsAgenda = () => import(/* webpackChunkName: 'appointment-agenda' */ './components/admin/AppointmentsAgenda.vue')
const StatusMetabox = () => import(/* webpackChunkName: 'status-metabox' */ './components/admin/StatusMetabox.vue')
const municipios = import(/* webpackChunkName: 'estados-municipios' */ '../json/municipios.json')

Vue.component('iande-appointments-agenda', AppointmentsAgenda)
Vue.component('iande-status-metabox', StatusMetabox)

async function populateCityOptions (state, city) {
    const $city = jQuery('select#city')
    $city.empty()

    for (const [key, value] of Object.entries(await municipios)) {
        if (key.startsWith(state)) {
            $city.append(jQuery('<option></option>').attr('value', key).text(value))
        }
    }

    if (city && city.startsWith(state)) {
        $city.val(city);
    } else {
        $city.val($city.find('option').first().val());
    }
}

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
                    jQuery('input#complement').val(res.complemento || '')
                    jQuery('input#district').val(res.bairro || '')
                    jQuery('select#state').val(res.uf)
                    await populateCityOptions(res.uf, `${res.uf}${res.ibge.slice(2)}`)
                }
            } catch (err) {
                console.error(err)
            }
        }
    })

    jQuery('select#state').change((e) => {
        const state = e.target.value
        const city = jQuery('select#city').val()
        populateCityOptions(state, city)
    })
})