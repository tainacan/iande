<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <InstitutionalGroupsChart :groups="filteredGroups"/>
        </div>
        <div class="iande-charts-grid">
            <RecurringVisitorsChart :appointments="filteredAppointments"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import InstitutionalGroupsChart from '@components/charts/InstitutionalGroups.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'
    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            InstitutionalGroupsChart,
            RecurringVisitorsChart
        },
        data () {
            return {
                dateFrom: DateTime.fromISO(today).minus({ month: 1 }).toISODate(),
                dateTo: today,
            }
        },
        computed: {
            appointmentsMap () {
                return arrayToMap(this.rawData.appointments, 'ID')
            },
            filteredAppointments () {
                return this.rawData.appointments
            },
            filteredGroups () {
                return this.rawData.groups.filter(group => {
                    return group.date >= this.dateFrom && group.date <= this.dateTo
                })
            },
            groupsMap () {
                return arrayToMap(this.rawData.groups, 'ID')
            },
            rawData: constant(window.IandeReports),
        }
    }
</script>
