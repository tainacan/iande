<template>
    <div class="iande-admin-agenda">
        <Calendar activeView="month" :disableViews="['years', 'year']" :events="events" locale="pt-br" startWeekOnSunday :timeStep="timeStep">
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div class="iande-admin-agenda__month">{{ cell.content }}</div>
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
            <template #event="{ event }">
                <div class="iande-admin-agenda__event">
                    <a class="iande-admin-agenda__group-link" :href="postLink(event.group)" target="_blank">
                        {{ event.group.name }}
                    </a>
                    <a class="iande-admin-agenda__appointment-link" :href="postLink(event.appointment)" target="_blank">
                        {{ event.appointment.name || `${event.appointment.responsible_first_name} ${event.appointment.responsible_last_name}` }}
                    </a>
                </div>
            </template>
        </Calendar>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Calendar from 'vue-cal'
    import { LocalScope } from 'vue-local-scope'
    import { get } from 'vuex-pathify'
    import 'vue-cal/dist/i18n/pt-br';

    import { api, toArray } from '../../utils'
    import { getWorkingHours } from '../../utils/agenda'

    export default {
        name: 'AppointmentsAgenda',
        components: {
            Calendar,
            LocalScope,
        },
        props: {
            exhibitionId: { type: Number, required: true },
        },
        data () {
            return {
                appointments: [],
                exhibition: null,
            }
        },
        computed: {
            groupsByDate () {
                const dates = new Map()
                for (const appointment of this.appointments) {
                    for (const group of (appointment.groups || [])) {
                        const data = { appointment, group }
                        const date = dates.get(group.date)
                        if (date) {
                            date.push(data)
                        } else {
                            dates.set(group.date, [data])
                        }
                    }
                }
                return dates
            },
            events () {
                const duration = this.exhibition ? Number(this.exhibition.duration) : 60
                const delta = { minutes: duration }
                const endsCache = new Map()
                return this.appointments.flatMap(appointment => {
                    return (appointment.groups || []).map(group => {
                        const start = group.hour
                        let end
                        if (endsCache.has(start)) {
                            end = endsCache.get(start)
                        } else {
                            end = DateTime.fromFormat(start, 'HH:mm').plus(delta).toFormat('HH:mm')
                            endsCache.set(start, end)
                        }
                        return {
                            start: `${group.date} ${start}`,
                            end: `${group.date} ${end}`,
                            appointment,
                            group,
                        }
                    })
                })
            },
            timeStep () {
                return this.exhibition ? Number(this.exhibition.grid) : 60
            },
            workingHours () {
                let start = null
                let end = null
                const week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
                for (const day of week) {
                    for (const interval of toArray(this.exhibition[day])) {
                        if (!start || start > interval.from) {
                            start = interval.from
                        }
                        if (!end || end < interval.to) {
                            end = interval.to
                        }
                    }
                }
                return { start, end }
            }
        },
        async created () {
            try {
                const exhibition = await api.post('exhibition/get', { ID: this.exhibitionId })
                this.exhibition = exhibition
                const appointments = await api.post('appointment/list_published', { exhibition: this.exhibitionId })
                this.appointments = appointments
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            postLink (post) {
                return `${window.IandeSettings.siteUrl}/wp-admin/post.php?post=${post.ID}&action=edit`
            },
            cellAppointments (cell) {
                return this.groupsByDate.get(cell.formattedDate) || []
            },
            cellHours (cell) {
                if (!this.exhibition) {
                    return []
                }
                return getWorkingHours(this.exhibition, cell.startDate).map(interval => `${interval.from} - ${interval.to}`)
            },
        }
    }
</script>
