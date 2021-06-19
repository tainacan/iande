<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Faixa etária dos grupos', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'AgeRangeChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            chartData () {

                let chartData = this.groups.reduce((increment, group) => {

                    if (typeof increment[group.age_range] !== 'undefined') {
                        increment[group.age_range].data = increment[group.age_range].data + 1
                    } else {
                        increment[group.age_range] = {
                            label: group.age_range,
                            data: 1
                        }
                    }

                    return increment
                }, [])

                return chartData

            },
            options () {
                return {
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '90%',
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -30,
                        style: {
                            fontSize: '12px',
                        }
                    },
                    xaxis: {
                        categories: Object.values(this.chartData).map(d => d.label),
                    },
                    colors: ['#1E2E55'],
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.9
                            }
                        }
                    }
                }
            },
            series () {
                return [
                    {
                        name: __('Grupos', 'iande'),
                        data: Object.values(this.chartData).map(d => d.data)
                    }
                ]
            },
        },
    }
</script>