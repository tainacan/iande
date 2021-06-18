__webpack_public_path__ = window.IandeSettings.iandePath

import Vue from 'vue'

import WpI18n from '@plugins/wp-i18n'
import { api } from '@utils'
import { cep } from '@utils/validators'

const ExhibitionAgenda = () => import(/* webpackChunkName: 'exhibition-agenda' */ '@components/admin/ExhibitionAgenda.vue')
const StatusMetabox = () => import(/* webpackChunkName: 'status-metabox' */ '@components/admin/StatusMetabox.vue')
const cities = import(/* webpackChunkName: 'estados-municipios' */ '../json/municipios.json')

Vue.use(WpI18n)

Vue.component('iande-exhibition-agenda', ExhibitionAgenda)
Vue.component('iande-status-metabox', StatusMetabox)

async function populateCityOptions (state, city) {
    const $city = jQuery('select#city')
    $city.empty()

    for (const [key, value] of Object.entries(await cities)) {
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
