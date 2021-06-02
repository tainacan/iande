<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <InstitutionalGroupsChart :groups="filteredGroups"/>
            <RecurringVisitorsChart :appointments="filteredAppointments"/>
            <GroupsByInstitutionChart :appointments="filteredAppointments" :institutions="institutionsMap" />
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import InstitutionalGroupsChart from '@components/charts/InstitutionalGroups.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'
    import GroupsByInstitutionChart from '@components/charts/GroupsByInstitution.vue'

    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            InstitutionalGroupsChart,
            RecurringVisitorsChart,
            GroupsByInstitutionChart
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
            institutionsMap () {
                return this.rawData.institutions
            },
            rawData: constant(window.IandeReports),
        }
    }
</script>
