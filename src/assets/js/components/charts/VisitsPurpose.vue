<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Objetivos da visita', 'iande') }}</h2>
        <ApexChart type="donut" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { toArray } from '@utils'

    export default {
        name: 'VisitsPurposeChart',
        props: {
            appointments: { type: Array, required: true },
        },
        computed: {
            labels () {
                return Object.keys(this.purposes).map(purpose => __(purpose, 'iande')).sort()
            },
            options () {
                return {
                    colors: ['#238B19', '#7DB6C5', '#A8DBBC', '#1E2E55', '#D49025', '#EBC891', '#FFAAAA', '#EC3F3F'],
                    dataLabels: {
                        dropShadow: {
                            enabled: false,
                        },
                    },
                    labels: this.labels,
                    legend: {
                        horizontalAlign: 'left',
                        itemMargin: {
                            horizontal: 10,
                            vertical: 3,
                        },
                        markers: {
                            height: 18,
                            radius: 0,
                            width: 18,
                        },
                        position: 'bottom',

                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.9,
                            },
                        },
                    },
                    stroke: {
                        show: false,
                        width: 0,
                    },
                }
            },
            purposes () {
                const chartData = {}

                for (const appointment of this.appointments) {
                    const purpose = appointment.purpose

                    if (purpose) {
                        const groups = toArray(appointment.groups).length
                        if (chartData[purpose]) {
                            chartData[purpose] += groups
                        } else {
                            chartData[purpose] = groups
                        }
                    }
                }

                return chartData
            },
            series () {
                return this.labels.map(purpose => this.purposes[purpose])
            },
        },
    }
</script>
