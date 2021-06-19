<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Objetivos da visita', 'iande') }}</h2>
        <ApexChart type="donut" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'
import appointments from '@store/appointments';

    export default {
        name: 'VisitsPurposeChart',
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
                        position: 'bottom',
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
                    dataLabels: {
                        dropShadow: {
                            enabled: false,
                        },
                    },
                    labels: Object.keys(this.chartData),
                    colors: ['#238B19', '#7DB6C5', '#A8DBBC', '#1E2E55', '#D49025', '#EBC891', '#FFAAAA', '#EC3F3F'],
                    stroke: {
                        show: false,
                        width: 0
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.9,
                            },
                        },
                    },
                }
            },
            series () {
                return Object.values(this.chartData).map(d => d.data)
            },
        },
    }
</script>
