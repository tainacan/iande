<template>
    <section class="iande-appointment">
        <div class="iande-appointment__summary" :class="showDetails || 'collapsed'">
            <div>
                <div class="iande-appointment__date">
                    <div class="iande-appointment__day">{{ day }}</div>
                    <div class="iande-appointment__month">{{ month }}</div>
                </div>
                <div class="iande-appointment__summary-main">
                    <h2>{{ name }}</h2>
                    <div class="iande-appointment__info">
                        <Icon icon="map-marker-alt"/>
                        <span>{{ siteName }} / #{{ appointment.ID }}</span>
                    </div>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'clock']"/>
                        <span>{{ hour }}</span>
                    </div>
                </div>
            </div>
            <div>
                <StepsIndicator inline :step="Number(appointment.step)"/>
                <div class="iande-appointment__toggle" :aria-label="showDetails ? 'Ocultar detalhes' : 'Exibir detalhes'" role="button" tabindex="0" @click="toggleDetails" @keypress.enter="toggleDetails">
                    <Icon :icon="showDetails ? 'minus-circle' : 'plus-circle'"/>
                </div>
            </div>
        </div>
        <div class="iande-appointment__details" v-if="showDetails">
            <div class="iande-appointment__boxes">
                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon :icon="['far', 'calendar']"/>Evento</h3>
                        <div class="iande-appointment__edit">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(1)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>{{ appointment.purpose }}</div>
                    <div>{{ date }}</div>
                    <div>{{ hour }}</div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="user"/>Responsável pela visita</h3>
                        <div class="iande-appointment__edit">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(2)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_first_name }} {{ appointment.responsible_last_name }}</div>
                        <div>{{ appointment.responsible_role }}</div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_email }}</div>
                        <div>{{ formatPhone(appointment.responsible_phone) }}</div>
                    </div>
                </div>

                <div class="iande-appointment__box" v-if="institution">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="university"/>Instituição</h3>
                        <div class="iande-appointment__edit">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(3)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>
                        <div>{{ institution.name }}</div>
                        <div>{{ formatPhone(institution.phone) }}</div>
                    </div>
                    <div>
                        <div>
                            {{ institution.address }}, {{ institution.address_number }}
                            <template v-if="institution.complement">{{ institution.complement }}</template>
                            - {{ institution.district }}
                        </div>
                        <div>{{ city }} - {{ institution.state }}</div>
                        <div>CEP {{ formatCep(institution.zip_code) }}</div>
                    </div>
                </div>
            </div>
            <div class="iande-appointment__buttons iande-form">
                <template v-if="appointment.step == 2">
                    <button class="iande-button solid" @click="cancelAppointment">
                        Cancelar reserva
                        <Icon icon="times"/>
                    </button>
                    <a class="iande-button primary" :href="`${iandeUrl}/appointment/confirm?ID=${appointment.ID}`">
                        Confirmar reserva
                        <Icon icon="check"/>
                    </a>
                </template>
            </div>
        </div>
    </section>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { get } from 'vuex-pathify'

    import StepsIndicator from './StepsIndicator'
    import { api, constant, formatCep, formatPhone } from '../utils'
    import '../utils/ibge'

    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

    export default {
        name: 'AppointmentDetails',
        components: {
            Icon: FontAwesomeIcon,
            StepsIndicator,
        },
        props: {
            appointment: { type: Object, required: true },
            n: { type: Number, default: 1 },
        },
        data () {
            return {
                showDetails: false,
            }
        },
        computed: {
            city () {
                const cityId = this.institution.city
                return Object.entries(window.municipios).find(([key]) => key === cityId)[1]
            },
            date () {
                const parts = this.appointment.date.split('-')
                return parts.reverse().join('/')
            },
            day () {
                const parts = this.appointment.date.split('-')
                return parts[2]
            },
            hour () {
                const parts = this.appointment.hour.split(':')
                return parts.join('h')
            },
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            institution () {
                return this.institutions.find(institution => institution.ID == this.appointment.institution_id)
            },
            institutions: get('institutions/list'),
            month() {
                const parts = this.appointment.date.split('-')
                return months[parseInt(parts[1]) - 1]
            },
            name () {
                if (this.appointment.name) {
                    return this.appointment.name
                } else {
                    return `Agendamento ${this.n}`
                }
            },
            siteName: constant(window.IandeSettings.siteName),
        },
        methods: {
            async cancelAppointment () {
                try {
                    await api.post('appointment/cancel', { ID: this.appointment.ID })
                    window.location.reload()
                } catch (err) {
                    console.error(err)
                }
            },
            formatCep,
            formatPhone,
            gotoScreen (screen) {
                return `${window.IandeSettings.iandeUrl}/appointment/edit?ID=${this.appointment.ID}&screen=${screen}`
            },
            toggleDetails () {
                this.showDetails = !this.showDetails
            },
        }
    }
</script>