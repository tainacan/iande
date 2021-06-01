<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Visitantes recorrentes', 'iande') }}</h2>
        <ApexChart type="pie" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'RecurringVisitorsChart',
        props: {
            appointments: { type: Array, required: true },
        },
        computed: {
            options () {
                return {
                    labels: [__('Já visitou o museu antes', 'iande'), __('Primiera vez no museu', 'iande')],
                }
            },
            series () {
                let returnedVisit = 0
                let firstVisit = 0

                for (const appointment of this.appointments) {
                    if (appointment.has_visited_previously === 'yes') {
                        returnedVisit++
                    } else {
                        firstVisit++
                    }
                }

                return [returnedVisit, firstVisit]
            },
        }
    }
</script>
