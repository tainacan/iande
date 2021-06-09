<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Objetivos da visita', 'iande') }}</h2>
        <ApexChart type="donut" height="450" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'
import appointments from '@store/appointments';

    export default {
        name: 'PurposeVisitChart',
        props: {
            appointments: { type: Array, required: true },
        },
        computed: {
            purpose () {
                let chartData = this.appointments.reduce((initValue, appointment) => {
                    if (typeof initValue[appointment.purpose] !== 'undefined') {
                        initValue[appointment.purpose] = {
                            num_appointment: parseInt(initValue[appointment.purpose].num_appointment) + parseInt(appointment.num_people)
                        }
                    } else {
                        initValue[appointment.purpose] = {
                            num_appointment: 1
                        }
                    }

                    return initValue

                }, [])
                return chartData;
            },
            options () {
                return {
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        markers: {
                            width: 18,
                            height: 18,
                            radius: 0,
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 3,
                        }
                    },
                    labels: [
                        'Desenvolver a cultura geral do grupo',
                        'Ilustrar os conteúdos que estou trabalhando com esse grupo',
                        'Iniciar a exploração/descoberta de um novo tema',
                        'Promover uma atividade de lazer',
                    ],
                }
            },
            series () {
                return [12, 23, 34, 77]
            },
        },
    }
</script>
