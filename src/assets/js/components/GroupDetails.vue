<template>
    <section class="iande-group -full-width" :class="{ boxed }">
        <div class="iande-appointment__summary iande-group__summary" :class="{ collapsed }">
            <div class="iande-appointment__date">
                <div>
                    <div class="iande-appointment__day">{{ day }}</div>
                    <div class="iande-appointment__month">{{ month }}</div>
                </div>
            </div>
            <div class="iande-appointment__summary-main">
                <h2 :class="status">{{ name }}</h2>
                <div class="iande-appointment__summary-row">
                    <div>
                        <div class="iande-appointment__info">
                            <Icon :icon="['far', 'image']"/>
                            <span>{{ __(exhibition.title, 'iande') }}</span>
                        </div>
                        <div class="iande-appointment__info">
                            <Icon :icon="['far', 'clock']"/>
                            <span>{{ group.hour }} - {{ endHour }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="iande-group__steps">
                            <div class="iande-group__step">
                                <div class="iande-group__step-icon" :class="{ active: group.educator_id }">
                                    <Icon :icon="group.educator_id ? 'circle-check' : 'circle-minus'"/>
                                </div>
                                <label>
                                    <span>{{ __('Mediação:', 'iande') }}</span>
                                    <select v-model="group.educator_id">
                                        <option :value="null">{{ __('Atribuir mediação', 'iande') }}</option>
                                        <option v-for="educator of educators" :key="educator.ID" :value="educator.ID">
                                            {{ educator.display_name }}
                                        </option>
                                    </select>
                                    <Icon icon="pen"/>
                                </label>
                            </div>
                            <div class="iande-group__step">
                                <div class="iande-group__step-icon" :class="{ active: group.has_checkin }">
                                    <Icon :icon="group.has_checkin ? 'circle-check' : 'circle-minus'"/>
                                </div>
                                {{ __('Check-in', 'iande') }}
                            </div>
                            <div class="iande-group__step">
                                <div class="iande-group__step-icon" :class="{ active: group.has_report }">
                                    <Icon :icon="group.has_report ? 'circle-check' : 'circle-minus'"/>
                                </div>
                                {{ __('Avaliação', 'iande') }}
                            </div>
                            <div class="iande-appointment__toggle" :aria-label="showDetails ? __('Ocultar detalhes', 'iande') : __('Exibir detalhes', 'iande')" role="button" tabindex="0" v-if="boxed" @click="toggleDetails" @keypress.enter="toggleDetails">
                                <Icon :icon="showDetails ? 'circle-minus' : 'circle-plus'"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iande-group__details" v-if="!collapsed">
            <div class="iande-appointment__boxes">
                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="bus"/> {{ name }}</h3>
                    </div>
                    <div>{{ group.age_range }}</div>
                    <div>{{ sprintf(__('previsão de %s visitantes', 'iande'), group.num_people) }}</div>
                    <div>{{ sprintf(_n('%s responsável', '%s responsáveis', group.num_responsible, 'iande'), group.num_responsible) }}</div>
                    <div>{{ group.scholarity }}</div>
                    <div>{{ __('Deficiências', 'iande') }}: {{ disabilities }}</div>
                    <div>{{ __('Idiomas', 'iande') }}: {{ languages }}</div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="user-large"/>{{ __('Responsável pela visita', 'iande') }}</h3>
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
                        <h3><Icon icon="landmark"/>{{ __('Instituição', 'iande') }}</h3>
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
                    </div>
                    <div>{{ __('Você já visitou o museu antes', 'iande') }}: {{ formatBinaryOption(appointment.has_visited_previously) }}</div>
                    <div>{{ __('Preparação', 'iande') }}: {{ formatBinaryOption(appointment.has_prepared_visit) }}</div>
                    <div v-if="appointment.additional_comment">{{ __('Comentários', 'iande') }}: {{ appointment.additional_comment }}</div>
                </div>
            </div>
            <div class="iande-appointment__buttons" v-if="isEducator">
                <a class="iande-button" :class="canEvaluate ? 'solid' : 'primary'" :href="$iandeUrl(`group/checkin?ID=${group.ID}`)" v-if="canCheckin">
                    {{ group.has_checkin === 'on' ? __('Editar check-in', 'iande') : __('Fazer-checkin', 'iande') }}
                </a>
                <a class="iande-button primary" :href="$iandeUrl(`group/report?ID=${group.ID}`)" v-if="canEvaluate">
                    {{ __('Avaliar visita', 'iande') }}
                </a>
            </div>
        </div>
        <div class="iande-appointment__toggle-button" role="button" tabindex="0" v-if="boxed" @click="toggleDetails" @keypress.enter="toggleDetails">
            {{ collapsed ? __('Exibir detalhes', 'iande') : __('Ocultar detalhes', 'iande') }}
        </div>
    </section>
</template>

<script>
    import { DateTime } from 'luxon'
    import { get } from 'vuex-pathify'

    import { __, _x, sprintf } from '@plugins/wp-i18n'
    import { api, formatCep, formatPhone, isOther, today } from '@utils'
    import { assignmentStatus } from '@utils/groups'

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
        name: 'GroupDetails',
        props: {
            boxed: { type: Boolean, default: false },
            educators: { type: Array, default: () => [] },
            group: { type: Object, required: true },
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
            appointment () {
                return this.appointments.find(appointment => appointment.ID == this.group.appointment_id)
            },
            appointments: get('appointments/list'),
            day () {
                const parts = this.group.date.split('-')
                return parts[2]
            },
            canCheckin () {
                return this.isEducator && this.group.date <= today
            },
            canEvaluate () {
                return this.isEducator && this.group.has_checkin && this.group.checkin_showed === 'yes' && !this.group.has_report && this.group.date <= today
            },
            city () {
                if (!this.institution) {
                    return null
                }
                const cityId = this.institution.city
                return Object.entries(this.cities).find(([key]) => key === cityId)[1]
            },
            collapsed () {
                return this.boxed && !this.showDetails
            },
            disabilities () {
                const disabilities = this.group.disabilities
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
            endHour () {
                const delta = { minutes: Number(this.exhibition.duration) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat(__('HH:mm', 'iande'))
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.group.exhibition_id)
            },
            exhibitions: get('exhibitions/list'),
            institution () {
                if (this.appointment.group_nature !== 'institutional') {
                    return null
                }
                return this.institutions.find(institution => institution.ID == this.appointment.institution_id)
            },
            institutions: get('institutions/list'),
            isEducator () {
                return this.status === 'assigned-self'
            },
            languages () {
                const languages = this.group.languages
                return [{ languages_name: __('Português', 'iande') }, ...(languages ?? [])]
                    .map(language => {
                        if (isOther(language.languages_name) && language.languages_other) {
                            return `${__(language.languages_name, 'iande')} / ${__(language.languages_other, 'iande')}`
                        } else {
                            return __(language.languages_name, 'iande')
                        }
                    })
                    .join(', ')
            },
            month() {
                const parts = this.group.date.split('-')
                return months[parseInt(parts[1]) - 1]
            },
            name () {
                const appointmentName = this.appointment.name || sprintf(__('Agendamento %s', 'iande'), this.appointment.ID)
                const groupName = this.group.name || sprintf(__('Grupo %s', 'iande'), this.group.ID)
                return `${appointmentName} / ${groupName}`
            },
            responsibleRole () {
                if (isOther(this.appointment.responsible_role) && this.appointment.responsible_role_other) {
                    return this.appointment.responsible_role_other
                } else {
                    return this.appointment.responsible_role
                }
            },
            status () {
                return assignmentStatus(this.group, this.user?.ID)
            },
            user: get('users/current'),
        },
        created () {
            if (this.group.educator_id == 0) {
                this.group.educator_id = null
            }
        },
        watch: {
            'group.educator_id': {
                async handler (newVal, oldVal) {
                    const { educator_id, ID } = this.group
                    if (oldVal == 0 && !newVal) {
                        return
                    } else if (educator_id) {
                        await api.post('group/assign_educator', { educator_id, ID })
                    } else {
                        await api.post('group/unassign_educator', { ID })
                    }
                },
            },
        },
        methods: {
            formatBinaryOption (option) {
                return option === 'yes' ? __('Sim', 'iande') : __('Não', 'iande')
            },
            formatCep,
            formatPhone,
            toggleDetails () {
                this.showDetails = !this.showDetails
            }
        }
    }
</script>
