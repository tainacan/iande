<template>
    <section class="iande-appointment">
        <div class="iande-appointment__summary" :class="showDetails || 'collapsed'">
            <div>
                <div class="iande-appointment__date">
                    <div>
                        <div class="iande-appointment__from" v-if="manyDates">a partir de</div>
                        <div class="iande-appointment__day">{{ day }}</div>
                        <div class="iande-appointment__month">{{ month }}</div>
                    </div>
                </div>
                <div class="iande-appointment__summary-main">
                    <h2>{{ name }}</h2>
                    <div class="iande-appointment__info">
                        <Icon icon="map-marker-alt"/>
                        <span>{{ $iande.siteName }} / #{{ appointment.ID }}</span>
                    </div>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'clock']"/>
                        <span>{{ hours }}</span>
                    </div>
                </div>
            </div>
            <div>
                <StepsIndicator inline :step="Number(appointment.step)" :status="appointment.post_status" :reason="appointment.reason_cancel"/>
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
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(2)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>{{ exhibition.title }}</div>
                    <div>Previsão de {{ appointment.num_people }} pessoas no total</div>
                    <div v-for="(group, i) of appointment.groups" :key="group.ID">
                        <div>Grupo {{ i + 1 }}: {{ formatDate(group.date) }}</div>
                        <div>{{ formatInterval(group.hour) }} (até {{ exhibition.group_size }} pessoas)</div>
                    </div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="user"/>Responsável pela visita</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(3)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_first_name }} {{ appointment.responsible_last_name }}</div>
                        <div>{{ responsibleRole }}</div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_email }}</div>
                        <div>{{ formatPhone(appointment.responsible_phone) }}</div>
                    </div>
                </div>

                <div class="iande-appointment__box" v-if="institution">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="university"/>Instituição</h3>
                        <div class="iande-appointment__edit" v-if="editable">
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

                <template v-if="appointment.step > 2">
                    <div class="iande-appointment__box" v-for="(group, i) in appointment.groups" :key="group.id">
                        <div class="iande-appointment__box-title">
                            <h3><Icon icon="users"/>Grupo {{ i + 1 }}: {{ group.name }}</h3>
                            <div class="iande-appointment__edit" v-if="editable">
                                <a class="iande-appointment__edit-link" :href="gotoScreen(5)">Editar</a>
                                <Icon icon="pencil-alt"/>
                            </div>
                        </div>
                        <div>{{ group.age_range }}</div>
                        <div>previsão de {{ group.num_people }} visitantes</div>
                        <div>{{ group.num_responsible == 1 ? '1 responsável' : `${group.num_responsible} responsáveis` }}</div>
                        <div>{{ group.scholarity }}</div>
                        <div>Deficiências: {{ groupDisabilities(group.disabilities) }}</div>
                        <div>Idiomas: {{ groupLanguages(group.languages) }}</div>
                    </div>

                    <div class="iande-appointment__box">
                        <div class="iande-appointment__box-title">
                            <h3><Icon :icon="['far', 'address-card']"/>Dados adicionais</h3>
                            <div class="iande-appointment__edit" v-if="editable">
                                <a class="iande-appointment__edit-link" :href="gotoScreen(6)">Editar</a>
                                <Icon icon="pencil-alt"/>
                            </div>
                        </div>
                        <div>Você já visitou o museu antes: {{ formatBinaryOption(appointment.has_visited_previously) }}</div>
                        <div>Preparação: {{ formatBinaryOption(appointment.has_prepared_visit) }}</div>
                        <div v-if="appointment.additional_comment">Comentários: {{ appointment.additional_comment }}</div>
                    </div>
                </template>
            </div>
            <div class="iande-appointment__buttons">
                <button class="iande-button solid" @click="cancelAppointment" v-if="cancelable">
                    Cancelar reserva
                    <Icon icon="times"/>
                </button>
                <a class="iande-button primary" :href="$iandeUrl(`appointment/confirm?ID=${appointment.ID}`)" v-if="editable && appointment.step == 2">
                    Confirmar reserva
                    <Icon icon="check"/>
                </a>
                <button class="iande-button primary" v-else-if="editable && appointment.step == 3" @click="sendConfirmation">
                    Finalizar reserva
                    <Icon icon="check"/>
                </button>
                <button class="iande-button disabled" v-if="appointment.post_status === 'pending'" disabled>
                    Aguardando confirmação
                    <Icon icon="spinner" spin/>
                </button>
            </div>
        </div>
        <AppointmentCancelModal :appointment="appointment" ref="cancelModal"/>
        <AppointmentSuccessModal ref="successModal"/>
    </section>
</template>

<script>
    import { DateTime } from 'luxon'
    import { get } from 'vuex-pathify'

    import AppointmentCancelModal from '@components/AppointmentCancelModal.vue'
    import AppointmentSuccessModal from '@components/AppointmentSuccessModal.vue'
    import StepsIndicator from '@components/StepsIndicator.vue'
    import { api, formatCep, formatPhone, isOther, sortBy } from '@utils'
    import { getInterval } from '@utils/agenda'

    const cities = import(/* webpackChunkName: 'estados-municipios' */ '../../json/municipios.json')
    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

    export default {
        name: 'AppointmentDetails',
        components: {
            AppointmentCancelModal,
            AppointmentSuccessModal,
            StepsIndicator,
        },
        props: {
            appointment: { type: Object, required: true },
        },
        data () {
            return {
                showDetails: false,
            }
        },
        asyncComputed: {
            cities: {
                get () {
                    return cities
                },
                default: {},
            },
        },
        computed: {
            cancelable () {
                return this.appointment.post_status !== 'canceled'
            },
            city () {
                if (!this.institution) {
                    return null
                }
                const cityId = this.institution.city
                return Object.entries(this.cities).find(([key]) => key === cityId)[1]
            },
            day () {
                const parts = this.firstGroup.date.split('-')
                return parts[2]
            },
            editable () {
                return this.appointment.post_status === 'draft'
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.appointment.exhibition_id)
            },
            exhibitions: get('exhibitions/list'),
            firstGroup () {
                return this.appointment.groups.slice().sort(sortBy(group => `${group.date} ${group.hour}`))[0]
            },
            hours () {
                const hours = this.appointment.groups.map(group => group.hour)
                return [...new Set(hours)].sort().join(', ')
            },
            institution () {
                return this.institutions.find(institution => institution.ID == this.appointment.institution_id)
            },
            institutions: get('institutions/list'),
            manyDates () {
                return [...new Set(this.appointment.groups.map(group => group.date))].length > 1
            },
            month() {
                const parts = this.firstGroup.date.split('-')
                return months[parseInt(parts[1]) - 1]
            },
            name () {
                if (this.appointment.name) {
                    return this.appointment.name
                } else {
                    return `Agendamento #${this.appointment.ID}`
                }
            },
            purpose () {
                if (isOther(this.appointment.purpose) && this.appointment.purpose_other) {
                    return this.appointment.purpose_other
                } else {
                    return this.appointment.purpose
                }
            },
            responsibleRole () {
                if (isOther(this.appointment.responsible_role) && this.appointment.responsible_role_other) {
                    return this.appointment.responsible_role_other
                } else {
                    return this.appointment.responsible_role
                }
            },
        },
        methods: {
            async cancelAppointment () {
                this.$refs.cancelModal.open()
            },
            formatBinaryOption (option) {
                return option === 'yes' ? 'Sim' : 'Não'
            },
            formatCep,
            formatDate (date) {
                return DateTime.fromISO(date).toLocaleString(DateTime.DATE_SHORT)
            },
            formatInterval (time) {
                const interval = getInterval(this.exhibition, time)
                return `${interval.start.toFormat('HH:mm')} - ${interval.end.toFormat('HH:mm')}`
            },
            formatPhone,
            gotoScreen (screen) {
                return this.$iandeUrl(`appointment/edit?ID=${this.appointment.ID}&screen=${screen}`)
            },
            groupDisabilities (disabilities) {
                if (disabilities.length === 0) {
                    return 'não'
                } else {
                    return disabilities
                        .map(disability => {
                            if (isOther(disability.disabilities_type) && disability.disabilities_other) {
                                return `${disability.disabilities_type} / ${disability.disabilities_other} (${disability.disabilities_count})`
                            } else {
                                return `${disability.disabilities_type} (${disability.disabilities_count})`
                            }
                        })
                        .join(', ')
                }
            },
            groupLanguages (languages) {
                return [{ languages_name: 'Português' }, ...languages]
                    .map(language => {
                        if (isOther(language.languages_name) && language.languages_other) {
                            return `${language.languages_name} / ${language.languages_other}`
                        } else {
                            return language.languages_name
                        }
                    })
                    .join(', ')
            },
            async sendConfirmation () {
                await api.post('appointment/set_status', { ID: this.appointment.ID, post_status: 'pending' })
                this.$refs.successModal.open()
            },
            toggleDetails () {
                this.showDetails = !this.showDetails
            },
        }
    }
</script>