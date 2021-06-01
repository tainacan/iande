<template>
    <div class="iande-educator-agenda">
        <Calendar activeView="month" :disableViews="disabledViews" :events="events" locale="pt-br" startWeekOnSunday :timeFrom="timeLimits.start" :timeStep="15" :timeTo="timeLimits.end">
            <template #cell-content="{ cell, view }">
                <template v-if="view.id === 'month'">
                    <div class="iande-admin-agenda__month">
                        <b>{{ cell.content }}</b>
                        <div class="iande-educator-agenda__month-row" aria-hidden="true">
                            <template v-for="count, status of cellGroups(cell)">
                                <span class="iande-educator-agenda__bubble" :class="status" :key="status" v-if="count > 0">
                                    {{ count }}
                                </span>
                            </template>
                        </div>
                    </div>
                </template>
            </template>
            <template #event="{ event }">
                <div class="iande-educator-agenda__event" :class="status(event.group)">
                    <p><b>{{ event.group.name }}</b></p>
                    <p>{{ event.group.hour }} - {{ formatHour(event.end) }}</p>
                    <a href="javascript:void(0)" role="button" tabindex="0" @click="showModal(event.group)">{{ __('ver mais', 'iande') }}</a>
                </div>
            </template>
        </Calendar>
        <GroupAssignmentModal ref="modal" :educators="educators"/>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Calendar from 'vue-cal'
    import { get } from 'vuex-pathify'
    import 'vue-cal/dist/i18n/pt-br'

    import GroupAssignmentModal from '@components/GroupAssignmentModal.vue'
    import { toArray } from '@utils'
    import { assignmentStatus } from '@utils/groups'

    const weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

    export default {
        name: 'GroupsCalendar',
        components: {
            Calendar,
            GroupAssignmentModal,
        },
        props: {
            educators: { type: Array, default: () => [] },
        },
        data () {
            return {
                disabledViews: ['years', 'year'],
            }
        },
        computed: {
            exhibitions: get('exhibitions/list'),
            groups: get('groups/list'),
            groupsByDate () {
                const dates = new Map()
                for (const group of this.groups) {
                    const data = { group }
                    const date = dates.get(group.date)
                    if (date) {
                        date.push(data)
                    } else {
                        dates.set(group.date, [data])
                    }
                }
                return dates
            },
            events () {
                return this.groups.map(group => {
                    const exhibition = this.exhibitionsMap.get(Number(group.exhibition_id))
                    const duration = exhibition?.duration ? Number(exhibition.duration) : 60
                    const start = group.hour
                    const delta = { minutes: duration }
                    const end = DateTime.fromFormat(start, 'HH:mm').plus(delta).toFormat('HH:mm')
                    return {
                        start: `${group.date} ${start}`,
                        end: `${group.date} ${end}`,
                        group,
                    }
                })
            },
            exhibitionsMap () {
                return new Map(this.exhibitions.map(exhibition => [Number(exhibition.ID), exhibition]))
            },
            timeLimits () {
                let start = '24:00'
                let end = '00:00'

                for (const exhibition of this.exhibitions) {
                    for (const day of weekDays) {
                        if (exhibition[day]) {
                            for (const hour of toArray(exhibition[day])) {
                                if (hour.from && hour.from < start) {
                                    start = hour.from
                                }
                                if (hour.to && hour.to > end) {
                                    end = hour.to
                                }
                            }
                        }
                    }
                    if (exhibition.exception) {
                        for (const exception of toArray(exhibition.exception)) {
                            for (const hour of toArray(exception.exceptions)) {
                                if (hour.from && hour.from < start) {
                                    start = hour.from
                                }
                                if (hour.to && hour.to > end) {
                                    end = hour.to
                                }
                            }
                        }
                    }
                }

                const [startHour, startMinute] = start.split(':')
                const [endHour, endMinute] = end.split(':')
                return {
                    start: (Number(startHour) * 60) + Number(startMinute),
                    end: (Number(endHour) * 60) + Number(endMinute),
                }
            },
            user: get('users/current'),
        },
        beforeMount () {
            if (window.matchMedia?.('(max-width: 800px)').matches) {
                this.disabledViews = [...this.disabledViews, 'week']
            }
        },
        methods: {
            cellGroups (cell) {
                const groups = this.groupsByDate.get(cell.formattedDate) || []
                const statuses = {
                    'assigned-other': 0,
                    'assigned-self': 0,
                    'unassigned': 0,
                }
                for (const group of groups) {
                    statuses[this.status(group.group)] += 1
                }
                return statuses
            },
            formatHour (date) {
                return DateTime.fromJSDate(date).toFormat('HH:mm')
            },
            showModal (group) {
                this.$refs.modal.open(group)
            },
            status (group) {
                return assignmentStatus(group, this.user?.ID)
            },
        }
    }
</script>
