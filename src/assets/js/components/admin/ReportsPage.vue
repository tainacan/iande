<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <VisitsByDateChart :groups="groups"/>
            <VisitsByExhibitionChart :exhibitions="exhibitions" :groups="groups"/>
            <ScholarityChart :groups="groups"/>
            <GroupsNatureChart :appointments="appointments" :groups="groups"/>
            <StatesChart :appointments="appointments" :groups="groups" :institutions="institutions"/>
            <CitiesChart :appointments="appointments" :groups="groups" :institutions="institutions"/>
            <RecurringVisitorsChart :appointments="appointments" :groups="groups"/>
            <AgeRangeChart :groups="groups"/>
            <VisitsPurposeChart :appointments="appointments" :groups="groups"/>
            <VisitsByInstitutionChart :appointments="appointments" :groups="groups" :institutions="institutions"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import AgeRangeChart from '@components/charts/AgeRange.vue'
    import CitiesChart from '@components/charts/Cities.vue'
    import GroupsNatureChart from '@components/charts/GroupsNature.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'
    import ScholarityChart from '@components/charts/Scholarity.vue'
    import StatesChart from '@components/charts/States.vue'
    import VisitsByDateChart from '@components/charts/VisitsByDate.vue'
    import VisitsByExhibitionChart from '@components/charts/VisitsByExhibition.vue'
    import VisitsByInstitutionChart from '@components/charts/VisitsByInstitution.vue'
    import VisitsPurposeChart from '@components/charts/VisitsPurpose.vue'

    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            AgeRangeChart,
            CitiesChart,
            GroupsNatureChart,
            RecurringVisitorsChart,
            ScholarityChart,
            StatesChart,
            VisitsByDateChart,
            VisitsByExhibitionChart,
            VisitsByInstitutionChart,
            VisitsPurposeChart,
        },
        data () {
            return {
                dateFrom: DateTime.fromISO(today).minus({ year: 1 }).toISODate(),
                dateTo: DateTime.fromISO(today).plus({ year: 1 }).toISODate(),
            }
        },
        computed: {
            appointments () {
                return arrayToMap(this.rawData.appointments, 'ID')
            },
            exhibitions () {
                return arrayToMap(this.rawData.exhibitions, 'ID')
            },
            groups () {
                return this.rawData.groups.filter(group => {
                    return group.date >= this.dateFrom && group.date <= this.dateTo
                })
            },
            institutions () {
                return arrayToMap(this.rawData.institutions, 'ID')
            },
            rawData: constant(window.IandeReports),
        }
    }
</script>
