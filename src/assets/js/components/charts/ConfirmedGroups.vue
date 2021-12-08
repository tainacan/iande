<template>
    <div class="iande-chart-wrapper -colspan-2">
        <h2>{{ __('Quantidade de grupos confirmados e cancelados', 'iande') }}</h2>
        <ApexChart type="line" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'ConfirmedGroupsChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            dates () {
                return Object.keys(this.groupsByDate).sort()
            },
            groupsByDate () {
                const chartData = {}

                for (const group of this.groups) {
                    const date = this.getDate(group)

                    if (date) {
                        const postStatus = group.post_status

                        if (postStatus === 'publish' || postStatus === 'canceled') {
                            if (!chartData[date]) {
                                chartData[date] = { canceled: 0, confirmed: 0 }
                            }

                            if (group.post_status === 'publish') {
                                chartData[date].confirmed += 1
                            } else {
                                chartData[date].canceled += 1
                            }
                        }
                    }
                }

                return chartData
            },
            labels () {
                return this.dates
            },
            options () {
                return {
                    chart: {
                        zoom: {
                            enabled: true,
                        }
                    },
                    colors: ['#A8DBBC', '#7DB6C5'],
                    dataLabels: {
                        enabled: false,
                    },
                    fill: {
                        colors: ['#EBFAF1', '#BBDEE0'],
                        opacity: 0.3,
                        type: 'solid',
                    },
                    labels: this.labels,
                    legend: {
                        horizontalAlign: 'center',
                            itemMargin: {
                            horizontal: 10,
                            vertical: 10,
                        },
                        markers: {
                            width: 18,
                            height: 18,
                            radius: 0,
                        },
                        position: 'top',
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 7,
                    },
                    xaxis: {
                        type: 'datetime',
                    },
                    yaxis: {
                        opposite: false,
                    },
                }
            },
            series () {
                const canceled = this.dates.map(date => this.groupsByDate[date].canceled)
                const confirmed = this.dates.map(date => this.groupsByDate[date].confirmed)

                return [
                    {
                        data: confirmed,
                        name: __('Grupos confirmados', 'iande'),
                        type: 'area',
                    },
                    {
                        data: canceled,
                        name: __('Grupos cancelados', 'iande'),
                        type: 'area',
                    },
                ]
            },
        },
        methods: {
            getDate (group) {
                return group.date || null
            },
        },
    }
</script>
