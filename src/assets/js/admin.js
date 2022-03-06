__webpack_public_path__ = window.IandeSettings.iandePath

import { library } from '@fortawesome/fontawesome-svg-core'
import { faChartColumn, faCircleCheck, faCircleExclamation, faUsers } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon  } from '@fortawesome/vue-fontawesome'
import Vue from 'vue'

import WpI18n from '@plugins/wp-i18n'
import { api } from '@utils'
import { cep } from '@utils/validators'

const ApexChart = () => import(/* webpackChunkName: 'reports-page' */ 'vue-apexcharts')
const ExhibitionAgenda = () => import(/* webpackChunkName: 'exhibition-agenda' */ '@components/admin/ExhibitionAgenda.vue')
const ReportsPage = () => import(/* webpackChunkName: 'reports-page' */ '@components/admin/ReportsPage.vue')
const StatusMetabox = () => import(/* webpackChunkName: 'status-metabox' */ '@components/admin/StatusMetabox.vue')
const ItineraryMetabox = () => import(/* webpackChunkName: 'itinerary-metabox' */ '@components/admin/ItineraryMetabox.vue')
const cities = import(/* webpackChunkName: 'estados-municipios' */ '../json/municipios.json')

library.add(faChartColumn, faCircleCheck, faCircleExclamation, faUsers)

Vue.use(WpI18n)

Vue.component('ApexChart', ApexChart)
Vue.component('iande-exhibition-agenda', ExhibitionAgenda)
Vue.component('iande-itinerary-metabox', ItineraryMetabox)
Vue.component('iande-reports-page', ReportsPage)
Vue.component('iande-status-metabox', StatusMetabox)
Vue.component('Icon', FontAwesomeIcon)

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
