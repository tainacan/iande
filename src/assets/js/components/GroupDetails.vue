<template>
    <section class="iande-group" :class="{ boxed }">
        <div class="iande-group__summary" :class="{ collapsed }">
            <div>
                <div class="iande-appointment__date">
                    <div>
                        <div class="iande-appointment__day">{{ day }}</div>
                        <div class="iande-appointment__month">{{ month }}</div>
                    </div>
                </div>
                <div class="iande-appointment__summary-main">
                    <h2 :class="status">{{ name }}</h2>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'image']"/>
                        <span>{{ exhibition.title }}</span>
                    </div>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'clock']"/>
                        <span>{{ group.hour }} - {{ endHour }}</span>
                    </div>
                </div>
            </div>
            <div class="iande-group__steps">
                <div class="iande-group__step">
                    <div class="iande-group__step-icon active">
                        <Icon icon="check"/>
                    </div>
                    <label>
                        <span>Mediação:</span>
                        <select v-model="group.educator_id">
                            <option :value="null">Atribuir mediação</option>
                            <option v-for="educator in educators" :key="educator.ID" :value="educator.ID">
                                {{ educator.display_name }}
                            </option>
                        </select>
                        <Icon icon="pencil-alt"/>
                    </label>
                </div>
                <div class="iande-group__step">
                    <div class="iande-group__step-icon" :class="{ active: !!group.has_checkin }">
                        <Icon :icon="group.has_checkin ? 'check' : 'minus'"/>
                    </div>
                    Check-in
                </div>
                <div class="iande-group__step">
                    <div class="iande-group__step-icon" :class="{ active: !!group.has_report }">
                        <Icon :icon="group.has_report ? 'check' : 'minus'"/>
                    </div>
                    Avaliação
                </div>
                <div class="iande-appointment__toggle" :aria-label="showDetails ? 'Ocultar detalhes' : 'Exibir detalhes'" role="button" tabindex="0" v-if="boxed" @click="toggleDetails" @keypress.enter="toggleDetails">
                    <Icon :icon="showDetails ? 'minus-circle' : 'plus-circle'"/>
                </div>
            </div>
        </div>
        <div class="iande-group__details" v-if="!collapsed">
            <div class="iande-appointment__boxes">
                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="users"/> {{ name }}</h3>
                    </div>
                    <div>{{ group.age_range }}</div>
                    <div>previsão de {{ group.num_people }} visitantes</div>
                    <div>{{ group.num_responsible == 1 ? '1 responsável' : `${group.num_responsible} responsáveis` }}</div>
                    <div>{{ group.scholarity }}</div>
                    <div>Pessoas com deficiência: {{ disabilities }}</div>
                    <div>Idiomas: {{ languages }}</div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="user"/>Responsável pela visita</h3>
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

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon :icon="['far', 'address-card']"/>Dados adicionais</h3>
                    </div>
                    <div>Você já visitou o museu antes: {{ formatBinaryOption(appointment.has_visited_previously) }}</div>
                    <div>Preparação: {{ formatBinaryOption(appointment.has_prepared_visit) }}</div>
                    <div v-if="appointment.additional_comment">Comentários: {{ appointment.additional_comment }}</div>
                </div>
            </div>
            <div class="iande-appointment__buttons" v-if="isEducator">
                <a class="iande-button" :class="canEvaluate ? 'primary' : 'solid'" :href="$iandeUrl(`group/checkin?ID=${group.ID}`)" v-if="canCheckin">
                    {{ group.has_checkin === 'on' ? 'Editar check-in' : 'Fazer-checkin' }}
                </a>
                <a class="iande-button primary" :href="$iandeUrl(`group/report?ID=${group.ID}`)" v-if="canEvaluate">
                    Avaliar visita
                </a>
            </div>
        </div>
        <div class="iande-group__toggle-button" role="button" tabindex="0" v-if="boxed" @click="toggleDetails" @keypress.enter="toggleDetails">
            {{ collapsed ? 'Exibir detalhes' : 'Ocultar detalhes' }}
        </div>
    </section>
</template>

<script>
    import { DateTime } from 'luxon'
    import { get } from 'vuex-pathify'

    import { api, formatPhone, isOther, today } from '../utils'
    import { assignmentStatus } from '../utils/groups'

    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

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
                return this.isEducator && this.group.has_checkin && !this.group.has_report && this.group.date <= today
            },
            collapsed () {
                return this.boxed && !this.showDetails
            },
            disabilities () {
                const disabilities = this.group.disabilities
                if (!disabilities || disabilities.length === 0) {
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
            endHour () {
                const delta = { minutes: Number(this.exhibition.duration) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat('HH:mm')
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.group.exhibition_id)
            },
            exhibitions: get('exhibitions/list'),
            isEducator () {
                return this.status === 'assigned-self'
            },
            languages () {
                const languages = this.group.languages
                return [{ languages_name: 'Português' }, ...(languages ?? [])]
                    .map(language => {
                        if (isOther(language.languages_name) && language.languages_other) {
                            return `${language.languages_name} / ${language.languages_other}`
                        } else {
                            return language.languages_name
                        }
                    })
                    .join(', ')
            },
            month() {
                const parts = this.group.date.split('-')
                return months[parseInt(parts[1]) - 1]
            },
            name () {
                const appointmentName = this.appointment.name || `Agendamento ${this.appointment.ID}`
                const groupName = this.group.name || `Grupo ${this.group.ID}`
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
        watch: {
            'group.educator_id': {
                async handler () {
                    const { educator_id, ID } = this.group
                    if (educator_id) {
                        await api.post('group/assign_educator', { educator_id, ID })
                    } else {
                        await api.post('group/unassign_educator', { ID })
                    }
                },
            },
        },
        methods: {
            formatBinaryOption (option) {
                return option === 'yes' ? 'Sim' : 'Não'
            },
            formatPhone,
            toggleDetails () {
                this.showDetails = !this.showDetails
            }
        }
    }
</script>
