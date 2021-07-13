<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Visitantes recorrentes', 'iande') }}</h2>
        <ApexChart type="pie" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'RecurringVisitorsChart',
        props: {
            appointments: { type: Object, required: true },
            groups: { type: Array, required: true },
        },
        computed: {
            options () {
                return {
                    colors: ['#A8DBBC', '#7DB6C5'],
                    labels: [__('Já visitou o museu antes', 'iande'), __('Primiera vez no museu', 'iande')],
                }
            },
            series () {
                let returnedVisit = 0
                let firstVisit = 0

                for (const group of this.groups) {
                    const recurring = this.isRecurring(group)

                    if (recurring === 'yes') {
                        returnedVisit++
                    } else if (recurring === 'no') {
                        firstVisit++
                    }
                }

                return [returnedVisit, firstVisit]
            },
        },
        methods: {
            isRecurring (group) {
                const appointmentId = group.appointment_id
                if (!appointmentId) {
                    return null
                }

                return this.appointments[appointmentId].has_visited_previously || null
            },
        },
    }
</script>
