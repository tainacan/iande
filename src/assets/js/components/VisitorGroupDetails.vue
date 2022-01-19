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
                <h2>{{ name }}</h2>
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
                        <!-- <StatusIndicator inline/> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import { DateTime } from 'luxon'
    import { get } from 'vuex-pathify'

    import StatusIndicator from '@components/StatusIndicator.vue'
    import { __, _x, sprintf } from '@plugins/wp-i18n'

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
        name: 'VisitorGroupDetails',
        components: {
            StatusIndicator,
        },
        props: {
            boxed: { type: Boolean, default: false },
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
            collapsed () {
                return this.boxed && !this.showDetails
            },
            endHour () {
                const delta = { minutes: Number(this.exhibition.duration) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat(__('HH:mm', 'iande'))
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.group.exhibition_id)
            },
            exhibitions: get('exhibitions/list'),
            month() {
                const parts = this.group.date.split('-')
                return months[parseInt(parts[1]) - 1]
            },
            name () {
                const appointmentName = this.appointment.name || sprintf(__('Agendamento %s', 'iande'), this.appointment.ID)
                const groupName = this.group.name || sprintf(__('Grupo %s', 'iande'), this.group.ID)
                return `${appointmentName} / ${groupName}`
            },
        },
    }
</script>
