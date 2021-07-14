<template>
    <div class="iande-chart-wrapper -colspan-2">
        <h2>{{ __('Grupos agendados por instituição', 'iande') }}</h2>
        <ApexChart type="bar" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
    import { sortBy } from '@utils'

    export default {
        name: 'VisitsByInstitutionChart',
        props: {
            groups: { type: Array, required: true },
            institutions: { type: Object, required: true },
            map: { type: Object, required: true },
        },
        computed: {
            categories () {
                return this.sortedInstitutions.map(institutionId => this.institutions[institutionId].post_title)
            },
            chartData () {
                const chartData = {}

                for (const group of this.groups) {
                    const institution = this.getInstitution(group)

                    if (institution) {
                        const institutionId = institution.ID

                        if (chartData[institutionId]) {
                            chartData[institutionId] += 1
                        } else {
                            chartData[institutionId] = 1
                        }
                    }
                }

                return chartData
            },
            options () {
                return {
                    dataLabels: {
                        enabled: true,
                        formatter: val => val,
                        offsetY: -30,
                        style: {
                            fontSize: '12px',
                        }
                    },
                    fill: {
                        colors: ['#A8DBBC'],
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 0,
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                        },
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.9,
                            },
                        },
                    },
                    xaxis: {
                        categories: this.categories,
                        position: 'bottom',
                    },
                    yaxis: {
                        labels: {
                            formatter: val => val,
                            show: true,
                        }
                    },
                }
            },
            series () {
                const institutions = this.sortedInstitutions.map(institutionId => this.chartData[institutionId])

                return [{
                    data: institutions,
                    name: __('Grupos', 'iande'),
                }]
            },
            sortedInstitutions () {
                return Object.entries(this.chartData).sort(sortBy(x => x[1])).map(x => x[0])
            },
        },
        methods: {
            getInstitution (group) {
                return this.map[group.ID] || null
            },
        },
    }
</script>
