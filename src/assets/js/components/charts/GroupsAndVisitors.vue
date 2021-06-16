<template>
    <div class="iande-chart-wrapper -colspan-2">
        <h2>{{ __('Quantidade de grupos e visitantes', 'iande') }}</h2>
        <ApexChart type="line" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'GroupsAndVisitorsChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            groupsByDate () {
                const chartData = {}

                for (const group of this.groups) {
                    if (group.date) {
                        if (!chartData[group.date]) {
                            chartData[group.date] = { num_group: 0, num_people: 0 }
                        }
                        const dateData = chartData[group.date]
                        dateData.num_group += 1
                        dateData.num_people += parseInt(group.num_people) || 0
                    }
                }

                return chartData
            },
            labels () {
                return Object.keys(this.groupsByDate).sort()
            },
            options () {

                return {
                    chart: {
                        zoom: {
                            enabled: false,
                        }
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: 'straight',
                        width: 7,
                    },
                    labels: this.labels,
                    xaxis: {
                        // type: 'datetime',
                    },
                    yaxis: {
                        opposite: false,
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        markers: {
                            width: 18,
                            height: 18,
                            radius: 0,
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 10,
                        }
                    },
                    colors: ['#A8DBBC', '#7DB6C5'],
                    fill: {
                        colors: ['transparent', '#7DB6C5'],
                        opacity: 0.4,
                        type: 'solid',
                    }
                }
            },
            series () {
                const groups = this.labels.map(date => this.groupsByDate[date].num_group)
                const people = this.labels.map(date => this.groupsByDate[date].num_people)

                return [
                    {
                        type: 'area',
                        name: __('Visitantes', 'iande'),
                        data: people,
                    },
                    {
                        type: 'area',
                        name: __('Grupos', 'iande'),
                        data: groups,
                    }

                ]
            }
        }
    }
</script>
