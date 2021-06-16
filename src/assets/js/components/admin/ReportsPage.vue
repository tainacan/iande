<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <GroupsAndVisitorsChart :groups="filteredGroups"/>
            <GroupsAndVisitorsByExhibitionChart :groups="filteredGroups" :exhibitions="filteredExhibitions"/>
            <InstitutionalGroupsChart :groups="filteredGroups"/>
            <RecurringVisitorsChart :appointments="filteredAppointments"/>
            <GroupsAgeRangeChart :groups="filteredGroups"/>
            <PurposeVisitChart :appointments="filteredAppointments"/>
            <GroupsByInstitutionChart :appointments="filteredAppointments" :institutions="rawData.institutions"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import GroupsAgeRangeChart from '@components/charts/GroupsAgeRange.vue'
    import GroupsAndVisitorsChart from '@components/charts/GroupsAndVisitors.vue'
    import GroupsAndVisitorsByExhibitionChart from '@components/charts/GroupsAndVisitorsByExhibition.vue'
    import GroupsByInstitutionChart from '@components/charts/GroupsByInstitution.vue'
    import InstitutionalGroupsChart from '@components/charts/InstitutionalGroups.vue'
    import PurposeVisitChart from '@components/charts/PurposeVisit.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'

    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            GroupsAgeRangeChart,
            GroupsAndVisitorsChart,
            GroupsAndVisitorsByExhibitionChart,
            GroupsByInstitutionChart,
            InstitutionalGroupsChart,
            PurposeVisitChart,
            RecurringVisitorsChart,
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
            filteredExhibitions () {
                return this.rawData.exhibitions
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
