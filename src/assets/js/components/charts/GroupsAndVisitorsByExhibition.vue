<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Quantidade de grupos e visitantes por exposições', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'
import exhibitions from '@store/exhibitions'

    export default {
        name: 'GroupsAndVisitorsByExhibitionChart',
        props: {
            exhibitions: { type: Object, required: true },
            groups: { type: Array, required: true },
        },
        computed: {
            categories () {
                return this.exhibitionsList.map(exhibitionId => __(this.exhibitions[exhibitionId].post_title, 'iande'))
            },
            exhibitionsList () {
                return Object.keys(this.groupsByExhibition)
                    .sort((a, b) => a - b)
            },
            groupsByExhibition () {
                const chartData = {}

                for (const group of this.groups) {
                    if (group.exhibition_id) {
                        if (!chartData[group.exhibition_id]) {
                            chartData[group.exhibition_id] = { num_group: 0, num_people: 0 }
                        }
                        const exhibitionData = chartData[group.exhibition_id]
                        exhibitionData.num_group += 1
                        exhibitionData.num_people += parseInt(group.num_people) || 0
                    }
                }

                return chartData
            },
            options () {
                return {
                    colors: ['var(--iande-primary-color)', 'var(--iande-tertiary-color)'],
                    dataLabels: {
                        enabled: true,
                        offsetY: -30,
                        style: {
                            colors: ['var(--iande-text-color)'],
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
    }
</script>