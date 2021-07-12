<template>
    <div class="iande-charts-wrapper">
        <div class="row date-range-fields">
            <p>{{ __('Data de apuração:', 'iande') }}</p>
            <div>
                <label for="date-from" class="iande-label">{{ _x('De', 'range', 'iande') }}</label>
                <Datepicker id="date-from" :format="_x('dd/MM/yyyy', 'vuejs-datepicker', 'iande')" v-model="dateFromRaw"/>
            </div>
            <div>
                <label for="date-to" class="iande-label">{{ _x('Até', 'range', 'iande') }}</label>
                <Datepicker id="date-to" :format="_x('dd/MM/yyyy', 'vuejs-datepicker', 'iande')" v-model="dateToRaw"/>
            </div>
        </div>

        <ChartsHeader :data="rawData"/>

        <div class="iande-charts-grid">
            <ConfirmedGroupsChart :groups="filteredGroups"/>
            <VisitorsAppearanceChart :groups="groups"/>
            <VisitsByExhibitionChart :exhibitions="exhibitions" :groups="groups"/>
            <ScholarityChart :groups="groups"/>
            <GroupsNatureChart :appointments="appointments" :groups="groups"/>
            <RecurringVisitorsChart :appointments="appointments" :groups="groups"/>
            <AgeRangeChart :groups="groups"/>
            <VisitsPurposeChart :appointments="appointments" :groups="groups"/>
            <StatesChart :groups="groups" :institutions="institutionsMap"/>
            <CitiesChart :groups="groups" :institutions="institutionsMap"/>
            <VisitsByInstitutionChart :groups="groups" :institutions="institutions" :map="institutionsMap"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Datepicker from 'vuejs-datepicker'

    import AgeRangeChart from '@components/charts/AgeRange.vue'
    import ChartsHeader from '@components/ChartsHeader.vue'
    import CitiesChart from '@components/charts/Cities.vue'
    import ConfirmedGroupsChart from '@components/charts/ConfirmedGroups.vue'
    import GroupsNatureChart from '@components/charts/GroupsNature.vue'
    import RecurringVisitorsChart from '@components/charts/RecurringVisitors.vue'
    import ScholarityChart from '@components/charts/Scholarity.vue'
    import StatesChart from '@components/charts/States.vue'
    import VisitorsAppearanceChart from '@components/charts/VisitorsAppearance.vue'
    import VisitsByExhibitionChart from '@components/charts/VisitsByExhibition.vue'
    import VisitsByInstitutionChart from '@components/charts/VisitsByInstitution.vue'
    import VisitsPurposeChart from '@components/charts/VisitsPurpose.vue'

    import { arrayToMap, constant, today } from '@utils'

    export default {
        name: 'ReportsPage',
        components: {
            AgeRangeChart,
            ChartsHeader,
            CitiesChart,
            ConfirmedGroupsChart,
            Datepicker,
            GroupsNatureChart,
            RecurringVisitorsChart,
            ScholarityChart,
            StatesChart,
            VisitorsAppearanceChart,
            VisitsByExhibitionChart,
            VisitsByInstitutionChart,
            VisitsPurposeChart,
        },
        data () {
            return {
                dateFromRaw: DateTime.fromISO(today).minus({ year: 1 }).toJSDate(),
                dateToRaw: DateTime.fromISO(today).plus({ year: 1 }).toJSDate(),
            }
        },
        computed: {
            appointments () {
                return arrayToMap(this.rawData.appointments, 'ID')
            },
            dateFrom () {
                return DateTime.fromJSDate(this.dateFromRaw).toISODate()
            },
            dateTo () {
                return DateTime.fromJSDate(this.dateToRaw).toISODate()
            },
            filteredGroups () {
                return this.rawData.groups.filter(group => {
                    return group.date >= this.dateFrom && group.date <= this.dateTo
                })
            },
            groups () {
                return this.filteredGroups.filter(group => {
                    return group.post_status === 'publish'
                })
            },
            exhibitions () {
                return arrayToMap(this.rawData.exhibitions, 'ID')
            },
            institutions () {
                return arrayToMap(this.rawData.institutions, 'ID')
            },
            institutionsMap () {
                const map = {}

                for (const group of this.rawData.groups) {
                    const appointment = this.appointments[group.appointment_id]

                    if (appointment.group_nature === 'institutional') {
                        map[group.ID] = this.institutions[appointment.institution_id] || null
                    } else {
                        map[group.ID] = null
                    }
                }

                return map
            },
            rawData: constant(window.IandeReports),
        }
    }
</script>
