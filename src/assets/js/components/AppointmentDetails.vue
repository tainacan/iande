<template>
    <section class="iande-appointment">
        <div class="iande-appointment__summary" :class="showDetails || 'collapsed'">
            <div class="iande-appointment__date">
                <div>
                    <div class="iande-appointment__from" v-if="manyDates">{{ __('a partir de', 'iande') }}</div>
                    <div class="iande-appointment__day">{{ day }}</div>
                    <div class="iande-appointment__month">{{ month }}</div>
                </div>
            </div>
            <div class="iande-appointment__summary-main">
                <h2>{{ name }}</h2>
                <div class="iande-appointment__summary-row">
                    <div>
                        <div class="iande-appointment__info">
                            <Icon :icon="['far', 'image']"/>
                            <span>{{ exhibition.title }}</span>
                        </div>
                        <div class="iande-appointment__info">
                            <Icon :icon="['far', 'clock']"/>
                            <span>{{ hours }}</span>
                        </div>
                    </div>
                    <div>
                        <StatusIndicator :status="appointment.post_status" :reason="appointment.reason_cancel"/>
                        <div class="iande-appointment__toggle" :aria-label="showDetails ? __('Ocultar detalhes', 'iande') : __('Exibir detalhes', 'iande')" role="button" tabindex="0" @click="toggleDetails" @keypress.enter="toggleDetails">
                            <Icon :icon="showDetails ? 'circle-minus' : 'circle-plus'"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iande-appointment__details" v-if="showDetails">
            <div class="iande-appointment__boxes">
                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon :icon="['far', 'calendar']"/>{{ __('Evento', 'iande') }}</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(2)">{{ __('Editar', 'iande') }}</a>
                            <Icon icon="pen"/>
                        </div>
                    </div>
                    <div>{{ __(exhibition.title, 'iande') }}</div>
                    <div>{{ sprintf(__('Previsão de %s pessoas no total', 'iande'), appointment.num_people) }}</div>
                    <div v-for="(group, i) of appointment.groups" :key="group.ID">
                        <div>{{ sprintf(__('Grupo %s: %s', 'iande'), i + 1, formatDate(group.date)) }}</div>
                        <div>{{ formatInterval(group.hour) }} ({{ sprintf(__('até %s pessoas', 'iande'), exhibition.group_size) }})</div>
                    </div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="user-large"/>{{ __('Responsável pela visita', 'iande') }}</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(4)">{{ __('Editar', 'iande') }}</a>
                            <Icon icon="pen"/>
                        </div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_first_name }} {{ appointment.responsible_last_name }}</div>
                        <div>{{ __(responsibleRole, 'iande') }}</div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_email }}</div>
                        <div>{{ formatPhone(appointment.responsible_phone) }}</div>
                    </div>
                </div>

                <div class="iande-appointment__box" v-if="institution">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="landmark"/>{{ __('Instituição', 'iande') }}</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(4)">{{ __('Editar', 'iande') }}</a>
                            <Icon icon="pen"/>
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
                        <div>{{ __('CEP', 'iande') }} {{ formatCep(institution.zip_code) }}</div>
                    </div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon :icon="['far', 'address-card']"/>{{ __('Dados adicionais', 'iande') }}</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(6)">{{ __('Editar', 'iande') }}</a>
                            <Icon icon="pen"/>
                        </div>
                    </div>
                    <div>{{ __('Você já visitou o museu antes', 'iande') }}: {{ formatBinaryOption(appointment.has_visited_previously) }}</div>
                    <div>{{ __('Preparação', 'iande') }}: {{ formatBinaryOption(appointment.has_prepared_visit) }}</div>
                    <div v-if="appointment.additional_comment">{{ __('Comentários', 'iande') }}: {{ appointment.additional_comment }}</div>
                </div>

                <div class="iande-appointment__box" v-for="(group, i) of appointment.groups" :key="group.id">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="bus"/>{{ sprintf(__('Grupo %s: %s', 'iande'), i + 1, group.name) }}</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(3)">{{ __('Editar', 'iande') }}</a>
                            <Icon icon="pen"/>
                        </div>
                    </div>
                    <div>{{ __(group.age_range, 'iande') }}</div>
                    <div>{{ sprintf(__('previsão de %s visitantes', 'iande'), group.num_people) }}</div>
                    <div>{{ sprintf(_n('%s responsável', '%s resposáveis', group.num_responsible, 'iande'), group.num_responsible) }}</div>
                    <div>{{ __(group.scholarity, 'iande') }}</div>
                    <div>{{ __('Deficiências', 'iande') }}: {{ groupDisabilities(group.disabilities) }}</div>
                    <div>{{ __('Idiomas', 'iande') }}: {{ groupLanguages(group.languages) }}</div>
                    <div class="iande-appointment__feedback-link" v-if="canEvaluate(group)">
                        <a :href="$iandeUrl(`group/feedback/?ID=${group.ID}`)">
                            {{ __('Avaliar visita', 'iande') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="iande-appointment__buttons">
                <button class="iande-button solid" @click="cancelAppointment" v-if="cancelable">
                    {{ __('Cancelar reserva', 'iande') }}
                    <Icon icon="xmark"/>
                </button>
                <button class="iande-button disabled" v-if="appointment.post_status === 'pending'" disabled>
                    {{ __('Aguardando confirmação', 'iande') }}
                    <Icon icon="spinner" spin/>
                </button>
            </div>
        </div>
        <div class="iande-appointment__toggle-button" role="button" tabindex="0" @click="toggleDetails" @keypress.enter="toggleDetails">
            {{ showDetails ? __('Exibir detalhes', 'iande') : __('Ocultar detalhes', 'iande') }}
        </div>
        <AppointmentCancelModal :appointment="appointment" ref="cancelModal"/>
    </section>
</template>

<script>
    import { DateTime } from 'luxon'
    import { get } from 'vuex-pathify'

    import AppointmentCancelModal from '@components/AppointmentCancelModal.vue'
    import StatusIndicator from '@components/StatusIndicator.vue'
    import { __, _x, sprintf } from '@plugins/wp-i18n'
    import { api, formatCep, formatPhone, isOther, sortBy } from '@utils'
    import { getInterval } from '@utils/agenda'

    const cities = import(/* webpackChunkName: 'estados-municipios' */ '../../json/municipios.json')
    const months = [
        _x('Jan', 'month', 'iande'),
        _x('Fev', 'month', 'iande'),
        _x('Mar', 'month', 'iande'),
        _x('Abr', 'month', 'iande'),
        _x('Mai', 'month', 'iande'),
        _x('Jun', 'month', 'iande'),
        _x('Jul', 'month', 'iande'),
        _x('Ago', 'month', 'iande'),
        _x('Set', 'month', 'iande'),
        _x('Out', 'month', 'iande'),
        _x('Nov', 'month', 'iande'),
        _x('Dez', 'month', 'iande')
    ]

    export default {
        name: 'AppointmentDetails',
        components: {
            AppointmentCancelModal,
            StatusIndicator,
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
                return this.appointment.post_status !== 'canceled' && !(this.appointment.groups || []).some(group => group.has_checkin)
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
                return this.appointment.post_status === 'draft' || this.appointment.post_status === 'pending'
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
                if (this.appointment.group_nature !== 'institutional') {
                    return null
                }
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
                    return sprintf(__('Agendamento %s', 'iande'), this.appointment.ID)
                }
            },
            purpose () {
                if (isOther(this.appointment.purpose) && this.appointment.purpose_other) {
                    return __(this.appointment.purpose_other, 'iande')
                } else {
                    return __(this.appointment.purpose, 'iande')
                }
            },
            responsibleRole () {
                if (isOther(this.appointment.responsible_role) && this.appointment.responsible_role_other) {
                    return __(this.appointment.responsible_role_other, 'iande')
                } else {
                    return __(this.appointment.responsible_role, 'iande')
                }
            },
        },
        methods: {
            async cancelAppointment () {
                this.$refs.cancelModal.open()
            },
            canEvaluate (group) {
                return group.has_checkin === 'on' && group.checkin_showed === 'yes'
            },
            formatBinaryOption (option) {
                return option === 'yes' ? __('Sim', 'iande') : __('Não', 'iande')
            },
            formatCep,
            formatDate (date) {
                return DateTime.fromISO(date).toLocaleString(DateTime.DATE_SHORT)
            },
            formatInterval (time) {
                const interval = getInterval(this.exhibition, time)
                return `${interval.start.toFormat(__('HH:mm', 'iande'))} - ${interval.end.toFormat(__('HH:mm', 'iande'))}`
            },
            formatPhone,
            gotoScreen (screen) {
                return this.$iandeUrl(`appointment/edit?ID=${this.appointment.ID}&screen=${screen}`)
            },
            groupDisabilities (disabilities) {
                if (!disabilities || disabilities.length === 0) {
                    return __('Não', 'iande')
                } else {
                    return disabilities
                        .map(disability => {
                            if (isOther(disability.disabilities_type) && disability.disabilities_other) {
                                return `${__(disability.disabilities_type, 'iande')} / ${__(disability.disabilities_other, 'iande')} (${disability.disabilities_count})`
                            } else {
                                return `${__(disability.disabilities_type, 'iande')} (${disability.disabilities_count})`
                            }
                        })
                        .join(', ')
                }
            },
            groupLanguages (languages) {
                return [{ languages_name: __('Português', 'iande') }, ...languages]
                    .map(language => {
                        if (isOther(language.languages_name) && language.languages_other) {
                            return `${__(language.languages_name, 'iande')} / ${__(language.languages_other, 'iande')}`
                        } else {
                            return __(language.languages_name, 'iande')
                        }
                    })
                    .join(', ')
            },
            toggleDetails () {
                this.showDetails = !this.showDetails
            },
        }
    }
</script>
