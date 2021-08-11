<template>
    <div class="iande-charts-wrapper">
        <div class="row date-range-fields">
            <p>{{ __('Filtrar:', 'iande') }}</p>
            <div>
                <Label for="date-from">{{ _x('Início', 'range', 'iande') }}</Label>
                <Datepicker id="date-from" :format="_x('dd/MM/yyyy', 'vuejs-datepicker', 'iande')" v-model="dateFromRaw"/>
            </div>
            <div>
                <Label for="date-to">{{ _x('Fim', 'range', 'iande') }}</Label>
                <Datepicker id="date-to" :format="_x('dd/MM/yyyy', 'vuejs-datepicker', 'iande')" v-model="dateToRaw"/>
            </div>
            <div>
                <Label for="exhibition">{{ __('Exposição', 'iande') }}</Label>
                <select id="exhibition" v-model="exhibition">
                    <option :value="null">{{ __('Todas as exposições', 'iande') }}</option>
                    <option v-for="e of rawData.exhibitions" :key="e.ID" :value="e.ID">{{ __(e.post_title, 'post_title') }}</option>
                </select>
            </div>
        </div>

        <ChartsHeader :data="rawData" :from="dateFrom" :groups="filteredGroups" :to="dateTo"/>

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
    import Label from '@components/Label.vue'
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
            Label,
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
                dateFromRaw: DateTime.fromISO(today).minus({ month: 1 }).toJSDate(),
                dateToRaw: DateTime.fromISO(today).toJSDate(),
                exhibition: null,
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
                    if (this.exhibition && group.exhibition_id != this.exhibition) {
                        return false
                    }
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
                    const appointmentId = group.appointment_id
                    if (!appointmentId) {
                        continue
                    }

                    const appointment = this.appointments[appointmentId]
                    if (appointment?.group_nature === 'institutional') {
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
