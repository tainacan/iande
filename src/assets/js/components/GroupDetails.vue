<template>
    <section class="iande-group">
        <div class="iande-group__summary">
            <div>
                <div class="iande-appointment__date">
                    <div>
                        <div class="iande-appointment__day">{{ day }}</div>
                        <div class="iande-appointment__month">{{ month }}</div>
                    </div>
                </div>
                <div class="iande-appointment__summary-main">
                    <h2>{{ name }}</h2>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'image']"/>
                        <span>{{ exhibition.title }}</span>
                    </div>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'clock']"/>
                        <span>{{ group.hour }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="iande-group__details">
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
        </div>
    </section>
</template>

<script>
    import { get } from 'vuex-pathify'

    import { formatPhone, isOther } from '../utils'

    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

    export default {
        name: 'GroupDetails',
        props: {
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
            disabilities () {
                const disabilities = this.group.disabilities
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
            endHour () {
                const delta = { minutes: Number(exhibition.duration) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat('HH:mm')
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.group.exhibition_id)
            },
            exhibitions: get('exhibitions/list'),
            languages () {
                const languages = this.group.languages
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
            name () {
                const appointmentName = this.appointment.name || `Agendamento ${this.appointment.ID}`
                const groupName = this.group.name || `Grupo ${this.group.ID}`
                return `${appointmentName} / ${groupName}`
            },
            month() {
                const parts = this.group.date.split('-')
                return months[parseInt(parts[1]) - 1]
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
            formatBinaryOption (option) {
                return option === 'yes' ? 'Sim' : 'Não'
            },
            formatPhone,
        }
    }
</script>
