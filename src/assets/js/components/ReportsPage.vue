<template>
    <div class="iande-charts-wrapper">
        <div class="iande-charts-grid">
            <InstitutionalGroupsChart :appointments="filteredAppointments"/>
        </div>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import InstitutionalGroupsChart from '@components/charts/InstitutionalGroups.vue'
    import { constant, today } from '@utils'

    const data = {
        appointments: [
            { ID: 1, group_nature: 'institutional' },
            { ID: 2, group_nature: 'institutional' },
            { ID: 3, group_nature: 'other' },
        ]
    }

    export default {
        name: 'ReportsPage',
        components: {
            InstitutionalGroupsChart,
        },
        data () {
            return {
                dateFrom: DateTime.fromISO(today).minus({ month: 1 }).toISODate(),
                dateTo: today,
            }
        },
        computed: {
            filteredAppointments () {
                return this.rawData.appointments
            },
            rawData: constant(data),
        }
    }
</script>
