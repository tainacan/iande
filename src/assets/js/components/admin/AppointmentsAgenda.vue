<template>
    <div class="iande-appointments-agenda">
        <Calendar activeView="month" :disableViews="['years', 'year']" :events="events" locale="pt-br" startWeekOnSunday>
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div>{{ cell.content }}</div>
                    <div v-if="!cell.outOfScope && cellHours(cell)">
                        {{ cellHours(cell) }}
                    </div>
                    <div v-if="!cell.outOfScope && cellAppointments(cell)">
                        {{ cellAppointments(cell).length }} agendamento{{ cellAppointments(cell).length > 1 ? 's' : '' }}
                    </div>
                </template>
            </template>
        </Calendar>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Calendar from 'vue-cal'
    import 'vue-cal/dist/i18n/pt-br';

    import { api, constant } from '../../utils'
    import { getWorkingHours } from '../../utils/agenda'

    export default {
        name: 'AppointmentsAgenda',
        components: {
            Calendar,
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
                return this.appointmentsByDate.get(cell.formattedDate)
            },
            cellHours (cell) {
                return getWorkingHours(cell.startDate)
                    .map(interval => `${interval.from}-${interval.to}`)
                    .join('; ')
            }
        }
    }
</script>
