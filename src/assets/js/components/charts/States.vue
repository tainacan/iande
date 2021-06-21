<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Estados de origem dos grupos', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { sortBy } from '@utils'

    import states from '../../../json/estados.json'

    export default {
        name: 'StatesChart',
        props: {
            appointments: { type: Object, required: true },
            groups: { type: Array, required: true },
            institutions: { type: Object, required: true },
        },
        computed: {
            categories () {
                return this.sortedStates.map(state => states[state].nome)
            },
            options () {
                return {
                    colors: ['#A8DBBC'],
                    dataLabels: {
                        enabled: true,
                        offsetY: -30,
                        style: {
                            fontSize: '12px',
                        }
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '90%',
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                            horizontal: false,
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.9,
                            }
                        }
                    },
                    xaxis: {
                        categories: this.categories,
                    },
                }
            },
            series () {
                const states = this.sortedStates.map(state => this.states[state])

                return [
                    {
                        data: states,
                        name: __('Grupos', 'iande'),
                    }
                ]
            },
            sortedStates () {
                return Object.entries(this.states).sort(sortBy(x => x[1])).map(x => x[0])
            },
            states () {
                const chartData = {}

                for (const group of this.groups) {
                    const state = this.getState(group)

                    if (state) {
                        if (!chartData[state]) {
                            chartData[state] = 0
                        }
                        chartData[state] += 1
                    }
                }

                return chartData
            },
        },
        methods: {
            getState (group) {
                const appointmentId = group.appointment_id
                if (!appointmentId) {
                    return null
                }

                const institutionId = this.appointments[appointmentId].institution_id
                if (!institutionId) {
                    return null
                }

                return this.institutions[institutionId].state
            }
        },
    }
</script>
