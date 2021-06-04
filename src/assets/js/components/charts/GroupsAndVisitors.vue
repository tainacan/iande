<template>
    <div class="iande-chart-wrapper">
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
            options () {
                return {
                    chart: {
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight',
                        width: 7
                    },
                    labels: ['Mar 20', 'Abr 20', 'Mai 20', 'Jun 20', 'Jul 20', 'Ago 20', 'Set 20', 'Out 20'],
                    xaxis: {
                        // type: 'datetime',
                    },
                    yaxis: {
                        opposite: false
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
                        },
                    },
                    colors: ['#7DB6C5', '#A8DBBC'],
                    fill: {
                        colors: ['#7DB6C5', 'transparent'],
                        opacity: 0.4,
                        type: 'solid',
                    },
                }
            },
            series () {
                return [
                    {
                        type: 'area',
                        name: "Grupos",
                        data: ['12', '45', '23', '29', '34', '43', '45', '62'],
                    },
                    {
                        type: 'area',
                        name: "Visitantes",
                        data: ['40', '80', '88', '40', '47', '77', '45', '20'],
                    }
                ]
            },
        }
    }
</script>
