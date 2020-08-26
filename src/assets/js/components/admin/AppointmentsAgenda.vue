<template>
    <div class="iande-admin-agenda">
        <Calendar activeView="month" :disableViews="['years', 'year']" :events="events" locale="pt-br" startWeekOnSunday>
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div class="iande-admin-agenda__month">{{ cell.content }}</div>
                    <template v-if="!cell.outOfScope">
                        <LocalScope :count="cellAppointments(cell).length" :hours="cellHours(cell)" v-slot="{ count, hours }">
                            <div class="iande-admin-agenda__line" v-for="hour of hours" :key="hour">
                                {{ hour }}
                            </div>
                            <div class="iande-admin-agenda__line" v-if="count > 0">
                                {{ count }} reserva{{ count > 1 ? 's' : '' }}
                            </div>
                        </LocalScope>
                    </template>
                </template>
            </template>
        </Calendar>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { DateTime } from 'luxon'
    import Calendar from 'vue-cal'
    import { LocalScope } from 'vue-local-scope'
    import 'vue-cal/dist/i18n/pt-br';

    import { api, constant } from '../../utils'
    import { getWorkingHours } from '../../utils/agenda'

    export default {
        name: 'AppointmentsAgenda',
        components: {
            Calendar,
            LocalScope,
        },
        data () {
            return {
                appointments: [],
            }
        },
        computed: {
            appointmentsByDate () {
                const dates = new Map()
                for (const appointment of this.appointments) {
                    const date = dates.get(appointment.date)
                    if (date) {
                        date.push(appointment)
                    } else {
                        dates.set(appointment.date, [appointment])
                    }
                }
                return dates
            },
            events () {
                const delta = { minutes: this.timeStep }
                const endsCache = new Map()
                return this.appointments.map(appointment => {
                    const start = appointment.hour
                    let end
                    if (endsCache.has(start)) {
                        end = endsCache.get(start)
                    } else {
                        end = DateTime.fromFormat(start, 'HH:mm').plus(delta).toFormat('HH:mm')
                        endsCache.set(start, end)
                    }
                    return {
                        start: `${appointment.date} ${start}`,
                        end: `${appointment.date} ${end}`,
                        title: appointment.name || 'Agendamento',
                        raw: appointment,
                    }
                })
            },
            timeStep: constant(Number(window.IandeSettings.duration)),
        },
        async created () {
            try {
                const appointments = await api.post('appointment/list_published')
                this.appointments = appointments
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            cellAppointments (cell) {
                return this.appointmentsByDate.get(cell.formattedDate) || []
            },
            cellHours (cell) {
                return getWorkingHours(cell.startDate).map(interval => `${interval.from} - ${interval.to}`)
            }
        }
    }
</script>
