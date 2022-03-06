<template>
    <div class="iande-admin-agenda">
        <Calendar activeView="month" :disableViews="disabledViews" :events="events" locale="pt-br" startWeekOnSunday :timeStep="timeStep">
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div class="iande-admin-agenda__month">{{ cell.content }}</div>
                    <div class="iande-admin-agenda__line" v-for="hour of cellHours(cell)" :key="hour">
                        {{ hour }}
                    </div>
                    <div class="iande-admin-agenda__line" v-if="cellGroups(cell).length > 0">
                        <GroupsCounter :groups="cellGroups(cell)"/>
                    </div>
                </template>
            </template>
            <template #event="{ event }">
                <div class="iande-admin-agenda__event" :class="[event.appointment.post_status]">
                    <a class="iande-admin-agenda__group-link" :href="postLink(event.group)" target="_blank">
                        <span>
                            {{ event.group.name }}
                        </span>
                        <span :title="sprintf(__('%s pessoas', 'iande'), event.group.num_people)">
                            <Icon icon="users"/>
                            {{ event.group.num_people }}
                        </span>
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
    import 'vue-cal/dist/i18n/pt-br'

    import GroupsCounter from '@components/admin/GroupsCounter.vue'
    import { api } from '@utils'
    import { getWorkingHours } from '@utils/agenda'

    export default {
        name: 'ExhibitionAgenda',
        components: {
            Calendar,
            GroupsCounter,
        },
        props: {
            exhibitionId: { type: Number, required: true },
        },
        data () {
            return {
                appointments: [],
                disabledViews: ['years', 'year'],
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
        },
        async created () {
            try {
                const exhibition = await api.post('exhibition/get', { ID: this.exhibitionId })
                this.exhibition = exhibition
                const appointments = await api.post('appointment/list_published', { exhibition: this.exhibitionId, pending: 1 })
                this.appointments = appointments
            } catch (err) {
                console.error(err)
            }
        },
        beforeMount () {
            if (window.matchMedia?.('(max-width: 800px)').matches) {
                this.disabledViews = [...this.disabledViews, 'week']
            }
        },
        methods: {
            cellGroups (cell) {
                return this.groupsByDate.get(cell.formattedDate) || []
            },
            cellHours (cell) {
                if (!this.exhibition) {
                    return []
                }
                return getWorkingHours(this.exhibition, cell.startDate).map(interval => `${interval.from} - ${interval.to}`)
            },
            confirmedGroups (cell) {
                const groups = this.cellGroups(cell)
                return groups.map(groups => groups.group.post_status === 'publish')
            },
            pendingGroups (cell) {
                const groups = this.cellGroups(cell)
                return groups.map(groups => groups.group.post_status === 'pending')
            },
            postLink (post) {
                return `${window.IandeSettings.siteUrl}/wp-admin/post.php?post=${post.ID}&action=edit`
            },
        }
    }
</script>
