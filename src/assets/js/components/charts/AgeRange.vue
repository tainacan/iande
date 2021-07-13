<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Faixa etária dos grupos', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { constant, sortBy } from '@utils'

    export default {
        name: 'AgeRangeChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            ageRanges () {
                const chartData = {}

                for (const group of this.groups) {
                    const range = this.getRange(group)

                    if (range) {
                        if (chartData[range]) {
                            chartData[range] += 1
                        } else {
                            chartData[range] = 1
                        }
                    }
                }

                return chartData
            },
            categories () {
                return this.rangesList.map(range => __(range, 'iande'))
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
            rangeOptions: constant(window.IandeSettings.ageRanges),
            rangesList () {
                return Object.keys(this.ageRanges).sort(sortBy(range => {
                    const index = this.rangeOptions.indexOf(range)
                    return index === -1 ? Number.MAX_SAFE_INTEGER : index
                }))
            },
            series () {
                const ranges = this.rangesList.map(range => this.ageRanges[range])
                return [
                    {
                        data: ranges,
                        name: __('Grupos', 'iande'),
                    }
                ]
            },
        },
        methods: {
            getRange (group) {
                if (group.checkin_age_range === 'no') {
                    return group.checkin_age_range_actual || null
                } else {
                    return group.age_range
                }
            },
        },
    }
</script>