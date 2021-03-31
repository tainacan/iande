<template>
    <div class="iande-educator-agenda">
        <Calendar activeView="month" :disableViews="['years', 'year']" :events="events" locale="pt-br" startWeekOnSunday>
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div class="iande-admin-agenda__month">{{ cell.content }}</div>
                    <LocalScope :groups="cellGroups(cell)" v-slot="{ groups }">
                        {{ groups.length }}
                    </LocalScope>
                </template>
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
            }
        },
        methods: {
            cellGroups (cell) {
                const groups = this.groupsByDate.get(cell.formattedDate) || []
                return groups
            },
        }
    }
</script>
