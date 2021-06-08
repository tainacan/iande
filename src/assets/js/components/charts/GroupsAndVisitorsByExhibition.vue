<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Quantidade de grupos e visitantes por exposições', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'GroupsAndVisitorsByExhibitionChart',
        props: {
            groups: { type: Array, required: true },
            exhibitions: { type: Array, required: true },
        },
        computed: {
            filterData () {
                let chartData = this.groups.reduce( (initValue, group) => {

                    let searchExhibition = this.exhibitions.map(function(e) {return e.ID}).indexOf(group.exhibition_id)
                    let exhibition = this.exhibitions[searchExhibition]

                    if (typeof initValue[exhibition.post_title] !== 'undefined') {
                        initValue[exhibition.post_title] = {
                            num_people: parseInt(initValue[exhibition.post_title].num_people) + parseInt(group.num_people),
                            num_group: parseInt(initValue[exhibition.post_title].num_group) + 1,
                        }
                    } else {
                        initValue[exhibition.post_title] = {
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
                            colors: ['var(--iande-text-color)']
                        }
                    },
                    xaxis: {
                        categories: this.exhibitions.map(exhibition => exhibition.post_title),
                    },
                    colors: ['var(--iande-primary-color)', 'var(--iande-tertiary-color)'],
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

                let exhibitions = Object.keys(this.filterData)

                let groups = []
                let peoples = []

                for(const exhibition in exhibitions) {
                    groups.push(this.filterData[exhibitions[exhibition]].num_group)
                    peoples.push(this.filterData[exhibitions[exhibition]].num_people)
                }

                return [
                    {
                        name: __('Grupos', 'iande'),
                        data: groups
                    }, {
                        name: __('Visitantes', 'iande'),
                        data: peoples
                    }
                ]
            },
        },
    }
</script>