<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Objetivos da visita', 'iande') }}</h2>
        <ApexChart type="donut" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'
import appointments from '@store/appointments';

    export default {
        name: 'PurposeVisitChart',
        props: {
            appointments: { type: Array, required: true },
        },
        computed: {
            chartData () {

                let chartData = this.appointments.reduce((increment, appointment) => {

                    if (typeof increment[appointment.purpose] !== 'undefined') {
                        increment[appointment.purpose].data = increment[appointment.purpose].data + 1
                    } else {
                        increment[appointment.purpose] = {
                            data: 1
                        }
                    }

                    return increment
                }, [])

                return chartData

            },
            options () {
                return {
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        markers: {
                            width: 18,
                            height: 18,
                            radius: 0,
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 3,
                        }
                    },
                    labels: Object.keys(this.chartData),
                }
            },
            series () {
                return Object.values(this.chartData).map(d => d.data)
            },
        },
    }
</script>
