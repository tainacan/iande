<template>
    <div class="iande-chart-wrapper -colspan-2">
        <h2>{{ __('Grupos agendados por instituição', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'GroupsByInstitutionChart',
        props: {
            appointments: { type: Array, required: true },
            institutions: { type: Array, required: true },
        },
        computed: {

            chartData () {
                let appointmentsWithInstitution = this.appointments.filter((appointment) => {
                    return typeof appointment.institution_id !== 'undefined'
                })

                let chartData = appointmentsWithInstitution.reduce((increment, appointment) => {
                    let searchInstitution = this.institutions.map(function(e) {return e.ID}).indexOf(appointment.institution_id)
                    let institution = this.institutions[searchInstitution]

                    if (typeof increment[institution.ID] !== 'undefined') {
                        increment[institution.ID].data = increment[institution.ID].data + appointment.groups.length
                    } else {
                        increment[institution.ID] = {
                            name: institution.post_title,
                            data: appointment.groups.length

                        }
                    }

                    return increment
                }, [])

                return chartData.filter(String)
            },
            options () {
                return {
                    plotOptions: {
                        bar: {
                            borderRadius: 0,
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val,
                        offsetY: -30,
                        style: {
                            fontSize: '12px',
                        }
                    },
                    xaxis: {
                        categories: this.chartData.map(d => d.name),
                        position: 'bottom',
                    },
                    yaxis: {
                        labels: {
                            show: true,
                            formatter: val => val,
                        }
                    },
                    fill: {
                        colors: ['#A8DBBC']
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
                return [{
                    name: __('Grupos', 'iande'),
                    data: this.chartData.map(d => d.data)
                }]
            },
        },
    }
</script>
