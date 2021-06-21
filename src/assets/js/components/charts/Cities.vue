<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Cidades de origem dos grupos', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { sortBy } from '@utils'

    import cities from '../../../json/municipios.json'

    export default {
        name: 'CitiesChart',
        props: {
            appointments: { type: Object, required: true },
            groups: { type: Array, required: true },
            institutions: { type: Object, required: true },
        },
        computed: {
            categories () {
                return this.sortedCities.map(city => cities[city])
            },
            cities () {
                const chartData = {}

                for (const group of this.groups) {
                    const city = this.getCity(group)

                    if (city) {
                        if (!chartData[city]) {
                            chartData[city] = 0
                        }
                        chartData[city] += 1
                    }
                }

                return chartData
            },
            options () {
                return {
                    colors: ['#7DB6C5'],
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
                            horizontal: true,
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
                const cities = this.sortedCities.map(city => this.cities[city])

                return [
                    {
                        data: cities,
                        name: __('Grupos', 'iande'),
                    }
                ]
            },
            sortedCities () {
                return Object.entries(this.cities).sort(sortBy(x => x[1])).map(x => x[0])
            },
        },
        methods: {
            getCity (group) {
                const appointmentId = group.appointment_id
                if (!appointmentId) {
                    return null
                }

                const institutionId = this.appointments[appointmentId].institution_id
                if (!institutionId) {
                    return null
                }

                return this.institutions[institutionId].city
            }
        },
    }
</script>
