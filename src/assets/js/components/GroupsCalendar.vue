<template>
    <div class="iande-educator-agenda">
        <Calendar activeView="month" :disableViews="['years', 'year']" :events="events" locale="pt-br" startWeekOnSunday :timeFrom="timeLimits.start" :timeStep="15" :timeTo="timeLimits.end">
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div class="iande-admin-agenda__month">
                        <b>{{ cell.content }}</b>
                        <div class="iande-educator-agenda__month-row" aria-hidden="true">
                            <LocalScope :groups="cellGroups(cell)" v-slot="{ groups }">
                                <span class="iande-educator-agenda__ball unassigned" v-if="groups.length > 0">
                                    {{ groups.length }}
                                </span>
                            </LocalScope>
                        </div>
                    </div>
                </template>
            </template>
            <template #event="{ event }">
                <div class="iande-educator-agenda__event unassigned">
                    <p><b>{{ event.group.name }}</b></p>
                    <p>{{ event.group.hour }} - {{ formatHour(event.end) }}</p>
                    <a href="javascript:void(0)">ver mais</a>
                </div>
            </template>
        </Calendar>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Calendar from 'vue-cal'
    import { LocalScope } from 'vue-local-scope'
    import 'vue-cal/dist/i18n/pt-br'

    export default {
        name: 'GroupsCalendar',
        components: {
            Calendar,
            LocalScope,
        },
        props: {
            exhibitions: { type: Array, required: true },
            groups: { type: Array, required: true },
        },
        computed: {
            groupsByDate () {
                const dates = new Map()
                for (const group of this.groups) {
                    const data = { group }
                    const date = dates.get(group.date)
                    if (date) {
                        dates.push(data)
                    } else {
                        dates.set(group.date, [data])
                    }
                }
                return dates
            },
            events () {
                return this.groups.map(group => {
                    const exhibition = this.exhibitionsList.get(Number(group.exhibition_id)) ?? 60
                    const start = group.hour
                    const delta = { minutes: Number(exhibition.duration) }
                    const end = DateTime.fromFormat(start, 'HH:mm').plus(delta).toFormat('HH:mm')
                    return {
                        start: `${group.date} ${start}`,
                        end: `${group.date} ${end}`,
                        group,
                    }
                })
            },
            exhibitionsList () {
                return new Map(this.exhibitions.map(exhibition => [Number(exhibition.ID), exhibition]))
            },
            timeLimits () {
                if (this.events.length === 0) {
                    return { start: 0, end: 24 * 60 }
                }

                let start = '24:00'
                let end = '00:00'

                for (const group of this.groups) {
                    const exhibition = this.exhibitionsList.get(Number(group.exhibition_id)) ?? 60
                    const groupStart = group.hour
                    const delta = { minutes: Number(exhibition.duration) }
                    const groupEnd = DateTime.fromFormat(groupStart, 'HH:mm').plus(delta).toFormat('HH:mm')
                    if (groupStart < start) {
                        start = groupStart
                    }
                    if (groupEnd > end) {
                        end = groupEnd
                    }
                }

                const [startHour, startMinute] = start.split(':')
                const [endHour, endMinute] = end.split(':')
                return {
                    start: (Number(startHour) * 60) + Number(startMinute),
                    end: (Number(endHour) * 60) + Number(endMinute),
                }
            },
        },
        methods: {
            cellGroups (cell) {
                const groups = this.groupsByDate.get(cell.formattedDate) || []
                return groups
            },
            formatHour (date) {
                return DateTime.fromJSDate(date).toFormat('HH:mm')
            }
        }
    }
</script>
