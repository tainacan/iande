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
            filterData () {

                let chartData = this.groups.reduce( (initValue, group) => {
                    if (typeof initValue[group.date] !== 'undefined') {
                        initValue[group.date] = {
                            num_people: parseInt(initValue[group.date].num_people) + parseInt(group.num_people),
                            num_group: parseInt(initValue[group.date].num_group) + 1,
                        }
                    } else {
                        initValue[group.date] = {
                            num_people: parseInt(group.num_people),
                            num_group: 1,
                        }
                    }
                    return initValue
                }, [])

                return chartData

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
                    labels: Object.keys(this.filterData).sort(),
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

                let dates = Object.keys(this.filterData).sort()

                let groups = []
                let peoples = []

                for(const date in dates) {
                    groups.push(this.filterData[dates[date]].num_group)
                    peoples.push(this.filterData[dates[date]].num_people)
                }

                return [
                    {
                        type: 'area',
                        name: "Visitantes",
                        data: peoples,
                    },
                    {
                        type: 'area',
                        name: "Grupos",
                        data: groups,
                    }

                ]
            }
        }
    }
</script>
