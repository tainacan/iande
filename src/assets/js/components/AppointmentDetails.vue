<template>
    <section class="iande-appointment">
        <div class="iande-appointment__summary">
            <div>
                <div class="iande-appointment__date">
                    <div class="iande-appointment__day">{{ day }}</div>
                    <div class="iande-appointment__month">{{ month }}</div>
                </div>
                <div class="iande-appointment__summary-main">
                    <h2>{{ name }}</h2>
                    <div class="iande-appointment__detail">
                        <Icon icon="map-marker-alt"/>
                        <span>{{ siteName }} / #{{ appointment.ID }}</span>
                    </div>
                    <div class="iande-appointment__detail">
                        <Icon :icon="['far', 'clock']"/>
                        <span>{{ hour }}</span>
                    </div>
                </div>
            </div>
            <div>
                <StepsIndicator inline :step="Number(appointment.step)"/>
            </div>
        </div>
    </section>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import StepsIndicator from './StepsIndicator'
    import { constant } from '../utils'

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
        computed: {
            day () {
                const parts = this.appointment.date.split('-')
                return parts[2]
            },
            hour () {
                const parts = this.appointment.hour.split(':')
                return parts.join('h')
            },
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
        }
    }
</script>