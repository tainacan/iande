<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Grupos agendados por instituição', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import ApexChart from 'vue-apexcharts'

    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'GroupsByInstitutionChart',
        components: {
            ApexChart,
        },
        props: {
            appointments: { type: Array, required: true },
            institutions: { type: Array, required: true },
        },
        computed: {
            categories () {
                let categories = []
                for (const institution of this.institutions) {
                    categories.push(institution.post_title)
                }

                return categories
            },
            options () {
                return {
                    plotOptions: {
                        bar: {
                            borderRadius: 10,
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val,
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ['var(--iande-text-color)']
                        }
                    },
                    xaxis: {
                        categories: this.categories,
                        position: 'bottom',
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            formatter: val => val,
                        }
                    },
                    fill: {
                        colors: ['var(--iande-primary-color)']
                    },
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
                let data = []

                for (const appointment of this.appointments) {
                    if (appointment.institution_id) {
                        data.push(appointment.groups.length)
                    }
                }

                return [{
                    name: __('Grupos', 'iande'),
                    data: data
                }]
            },
        },
    }
</script>
