<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Cidades de origem dos grupos', 'iande') }}</h2>
        <select v-model="state">
            <option v-for="(name, uf) of stateOptions" :key="uf" :value="uf">{{ name }}</option>
        </select>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { sortBy } from '@utils'

    import cities from '../../../json/municipios.json'
    import states from '../../../json/estados.json'

    export default {
        name: 'CitiesChart',
        props: {
            groups: { type: Array, required: true },
            institutions: { type: Object, required: true },
        },
        data () {
            return {
                state: '',
            }
        },
        computed: {
            categories () {
                return this.sortedCities.map(city => cities[city])
            },
            chartData () {
                const chartData = {}

                for (const group of this.groups) {
                    const city = this.getCity(group)
                    const state = this.getState(group)

                    if (city && state) {
                        if (!chartData[state]) {
                            chartData[state] = { count: 0, items: {} }
                        }

                        const stateData = chartData[state]
                        stateData.count++
                        if (stateData.items[city]) {
                            stateData.items[city] += 1
                        } else {
                            stateData.items[city] = 1
                        }
                    }
                }

                return chartData
            },
            cities () {
                if (this.state) {
                    return this.chartData[this.state]?.items || {}
                } else {
                    return {}
                }
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
                return Object.entries(this.cities)
                    .filter(x => x[0].startsWith(this.state))
                    .sort(sortBy(x => x[1]))
                    .map(x => x[0])
            },
            stateOptions () {
                const entries = Object.values(states)
                    .map(state => [state.sigla, state.nome])
                    .sort(sortBy(state => state[1]))
                return Object.fromEntries(entries)
            },
        },
        watch: {
            chartData: {
                handler () {
                    if (!this.state) {
                        let maxCount = 0
                        let popularState = ''

                        for (const [state, stateData] of Object.entries(this.chartData)) {
                            if (stateData.count > maxCount) {
                                popularState = state
                                maxCount = stateData.count
                            }
                        }

                        this.state = popularState
                    }
                },
                immediate: true,
            },
        },
        methods: {
            getCity (group) {
                const institution = this.institutions[group.ID]
                if (!institution) {
                    return null
                }

                return institution.city
            },
            getState (group) {
                const institution = this.institutions[group.ID]
                if (!institution) {
                    return null
                }

                return institution.state
            },
        },
    }
</script>
