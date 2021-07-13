<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Objetivos da visita', 'iande') }}</h2>
        <ApexChart type="donut" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'VisitsPurposeChart',
        props: {
            appointments: { type: Object, required: true },
            groups: { type: Array, required: true },
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

                for (const group of this.groups) {
                    const purpose = this.getPurpose(group)

                    if (purpose) {
                        if (chartData[purpose]) {
                            chartData[purpose] += 1
                        } else {
                            chartData[purpose] = 1
                        }
                    }
                }

                return chartData
            },
            series () {
                return this.labels.map(purpose => this.purposes[purpose])
            },
        },
        methods: {
            getPurpose (group) {
                const appointmentId = group.appointment_id
                if (!appointmentId) {
                    return null
                }

                return this.appointments[appointmentId].purpose || null
            },
        },
    }
</script>
