<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Escolaridade dos grupos', 'iande') }}</h2>
        <ApexChart type="donut" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { constant, sortBy } from '@utils'

    export default {
        name: 'ScholarityChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            labels () {
                return this.scholaritiesList.map(scholarity => __(scholarity, 'iande')).sort()
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
                    stroke: {
                        show: false,
                        width: 0,
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
            scholarities () {
                const chartData = {}

                for (const group of this.groups) {
                    const scholarity = group.scholarity

                    if (scholarity) {
                        if (chartData[scholarity]) {
                            chartData[scholarity] += 1
                        } else {
                            chartData[scholarity] = 1
                        }
                    }
                }

                return chartData
            },
            scholaritiesList () {
                return Object.keys(this.scholarities).sort(sortBy(scholarity => {
                    const index = this.scholarityOptions.indexOf(scholarity)
                    return index === -1 ? Number.MAX_SAFE_INTEGER : index
                }))
            },
            scholarityOptions: constant(window.IandeSettings.scholarity),
            series () {
                return this.scholaritiesList.map(scholarity => this.scholarities[scholarity])
            },
        },
        methods: {
            getScholarity (group) {
                if (group.checkin_scholarity === 'no') {
                    return group.checkin_scholarity_actual || null
                } else {
                    return group.age_range
                }
            },
        },
    }
</script>
