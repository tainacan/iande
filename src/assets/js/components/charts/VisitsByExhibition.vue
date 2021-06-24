<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Quantidade de grupos e visitantes por exposições', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'VisitsByExhibitionChart',
        props: {
            exhibitions: { type: Object, required: true },
            groups: { type: Array, required: true },
        },
        computed: {
            categories () {
                return this.exhibitionsList.map(exhibitionId => __(this.exhibitions[exhibitionId].post_title, 'iande'))
            },
            exhibitionsList () {
                return Object.keys(this.groupsByExhibition).sort((a, b) => a - b)
            },
            groupsByExhibition () {
                const chartData = {}

                for (const group of this.groups) {
                    const exhibitionId = this.getExhibition(group)

                    if (exhibitionId) {
                        if (!chartData[exhibitionId]) {
                            chartData[exhibitionId] = { num_group: 0, num_people: 0 }
                        }
                        const exhibitionData = chartData[exhibitionId]
                        exhibitionData.num_group += 1
                        exhibitionData.num_people += this.getNumPeople(group)
                    }
                }

                return chartData
            },
            options () {
                return {
                    colors: ['#7DB6C5', '#A8DBBC'],
                    dataLabels: {
                        enabled: true,
                        offsetY: -30,
                        style: {
                            fontSize: '12px',
                        },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '90%',
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                            horizontal: false,
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
                    },
                }
            },
            series () {
                const groups = this.exhibitionsList.map(exhibition => this.groupsByExhibition[exhibition].num_group)
                const people = this.exhibitionsList.map(exhibition => this.groupsByExhibition[exhibition].num_people)

                return [
                    {
                        data: groups,
                        name: __('Grupos', 'iande'),
                        type: 'bar',
                    },
                    {
                        data: people,
                        name: __('Visitantes', 'iande'),
                        type: 'bar',
                    }
                ]
            },
        },
        methods: {
            getExhibition (group) {
                return group.exhibition_id || null
            },
            getNumPeople (group) {
                if (group.checkin_num_people === 'no') {
                    return parseInt(group.checkin_num_people_actual || group.num_people) || 0
                } else {
                    return parseInt(group.num_people) || 0
                }
            },
        },
    }
</script>