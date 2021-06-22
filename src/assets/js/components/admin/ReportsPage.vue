<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <VisitsByDateChart :groups="filteredGroups"/>
            <VisitsByExhibitionChart :groups="filteredGroups" :exhibitions="exhibitionsMap"/>
            <ScholarityChart :groups="filteredGroups"/>
            <GroupsNatureChart :appointments="appointmentsMap" :groups="filteredGroups"/>
            <StatesChart :appointments="appointmentsMap" :groups="filteredGroups" :institutions="institutionsMap"/>
            <CitiesChart :appointments="appointmentsMap" :groups="filteredGroups" :institutions="institutionsMap"/>
            <RecurringVisitorsChart :appointments="appointmentsMap" :groups="filteredGroups"/>
            <AgeRangeChart :groups="filteredGroups"/>
            <VisitsPurposeChart :appointments="appointmentsMap" :groups="filteredGroups"/>
            <GroupsByInstitutionChart :appointments="filteredAppointments" :institutions="rawData.institutions"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import AgeRangeChart from '@components/charts/AgeRange.vue'
    import CitiesChart from '@components/charts/Cities.vue'
    import GroupsByInstitutionChart from '@components/charts/GroupsByInstitution.vue'
    import GroupsNatureChart from '@components/charts/GroupsNature.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'
    import ScholarityChart from '@components/charts/Scholarity.vue'
    import StatesChart from '@components/charts/States.vue'
    import VisitsByDateChart from '@components/charts/VisitsByDate.vue'
    import VisitsByExhibitionChart from '@components/charts/VisitsByExhibition.vue'
    import VisitsPurposeChart from '@components/charts/VisitsPurpose.vue'

    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            AgeRangeChart,
            CitiesChart,
            GroupsByInstitutionChart,
            GroupsNatureChart,
            RecurringVisitorsChart,
            ScholarityChart,
            StatesChart,
            VisitsByDateChart,
            VisitsByExhibitionChart,
            VisitsPurposeChart,
        },
        data () {
            return {
                dateFrom: DateTime.fromISO(today).minus({ year: 1 }).toISODate(),
                dateTo: DateTime.fromISO(today).plus({ year: 1 }).toISODate(),
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
            institutionsMap () {
                return arrayToMap(this.rawData.institutions, 'ID')
            },
            rawData: constant(window.IandeReports),
        }
    }
</script>
