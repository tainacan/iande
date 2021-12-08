<template>
    <div class="iande-chart-wrapper -colspan-2">
        <h2>{{ __('Comparecimento de visitantes', 'iande') }}</h2>
        <ApexChart type="line" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'VisitorsAppearanceChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            dates () {
                return Object.keys(this.visitorsByDate).sort()
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
                const noshow = this.dates.map(date => this.visitorsByDate[date].noshow)
                const show = this.dates.map(date => this.visitorsByDate[date].show)

                return [
                    {
                        data: show,
                        name: __('Visitantes efetivos', 'iande'),
                        type: 'area',
                    },
                    {
                        data: noshow,
                        name: __('Visitantes faltantes', 'iande'),
                        type: 'area',
                    },
                ]
            },
            visitorsByDate () {
                const chartData = {}

                for (const group of this.groups) {
                    const date = this.getDate(group)

                    if (date) {
                        const status = this.getStatus(group)

                        if (status) {
                            if (!chartData[date]) {
                                chartData[date] = { noshow: 0, show: 0 }
                            }

                            if (status === 'yes') {
                                chartData[date].show += this.getNumPeople(group)
                            } else if (status === 'no') {
                                chartData[date].noshow += this.getNumPeople(group)
                            }
                        }
                    }
                }

                return chartData
            },
        },
        methods: {
            getDate (group) {
                return group.date || null
            },
            getNumPeople (group) {
                if (group.checkin_num_people === 'no') {
                    return parseInt(group.checkin_num_people_actual || group.num_people) || 0
                } else {
                    return parseInt(group.num_people) || 0
                }
            },
            getStatus (group) {
                if (group.has_checkin !== 'on') {
                    return null
                }
                return group.checkin_showed || null
            },
        },
    }
</script>
