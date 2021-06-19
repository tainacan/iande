<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <VisitsByDateChart :groups="filteredGroups"/>
            <VisitsByExhibitionChart :groups="filteredGroups" :exhibitions="exhibitionsMap"/>
            <ScholarityChart :groups="filteredGroups"/>
            <GroupsNatureChart :groups="filteredGroups"/>
            <RecurringVisitorsChart :appointments="filteredAppointments"/>
            <AgeRangeChart :groups="filteredGroups"/>
            <VisitsPurposeChart :appointments="filteredAppointments"/>
            <GroupsByInstitutionChart :appointments="filteredAppointments" :institutions="rawData.institutions"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import AgeRangeChart from '@components/charts/AgeRange.vue'
    import GroupsByInstitutionChart from '@components/charts/GroupsByInstitution.vue'
    import GroupsNatureChart from '@components/charts/GroupsNature.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'
    import ScholarityChart from '@components/charts/Scholarity.vue'
    import VisitsByDateChart from '@components/charts/VisitsByDate.vue'
    import VisitsByExhibitionChart from '@components/charts/VisitsByExhibition.vue'
    import VisitsPurposeChart from '@components/charts/VisitsPurpose.vue'

    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            AgeRangeChart,
            GroupsByInstitutionChart,
            GroupsNatureChart,
            RecurringVisitorsChart,
            ScholarityChart,
            VisitsByDateChart,
            VisitsByExhibitionChart,
            VisitsPurposeChart,
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
            exhibitionsMap () {
                return arrayToMap(this.rawData.exhibitions, 'ID')
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
