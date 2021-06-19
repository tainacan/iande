<template>
    <div class="iande-chart-wrapper -colspan-2">
        <h2>{{ __('Quantidade de grupos e visitantes', 'iande') }}</h2>
        <ApexChart type="line" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'

    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'VisitsByDateChart',
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
                    const date = group.date

                    if (date) {
                        if (!chartData[date]) {
                            chartData[date] = { num_group: 0, num_people: 0 }
                        }
                        const dateData = chartData[date]
                        dateData.num_group += 1
                        dateData.num_people += parseInt(group.num_people) || 0
                    }
                }

                return chartData
            },
            labels () {
                return this.dates.map(date => DateTime.fromISO(date).toLocaleString(DateTime.DATE_SHORT))
            },
            options () {
                return {
                    chart: {
                        zoom: {
                            enabled: false,
                        }
                    },
                    colors: ['#7DB6C5', '#A8DBBC'],
                    dataLabels: {
                        enabled: false,
                    },
                    fill: {
                        colors: ['#BBDEE0', '#EBFAF1'],
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
                        curve: 'straight',
                        width: 7,
                    },
                    /*
                    xaxis: {
                        type: 'datetime',
                    },
                    */
                    yaxis: {
                        opposite: false,
                    },
                }
            },
            series () {
                const groups = this.dates.map(date => this.groupsByDate[date].num_group)
                const people = this.dates.map(date => this.groupsByDate[date].num_people)

                return [
                    {
                        data: groups,
                        name: __('Grupos', 'iande'),
                        type: 'area',
                    },
                    {
                        data: people,
                        name: __('Visitantes', 'iande'),
                        type: 'area',
                    },
                ]
            },
        },
    }
</script>
