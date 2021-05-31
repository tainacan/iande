<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Natureza dos grupos', 'iande') }}</h2>
        <ApexChart type="pie" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'InstitutionalGroupsChart',
        props: {
            appointments: { type: Array, required: true },
        },
        computed: {
            options () {
                return {
                    labels: [__('Grupo Institucional', 'iande'), _x('Outra', 'group', 'iande')],
                }
            },
            series () {
                let institutionalNature = 0
                let otherNature = 0

                for (const appointment of this.appointments) {
                    if (appointment.group_nature === 'institutional') {
                        institutionalNature++
                    } else {
                        otherNature++
                    }
                }

                return [institutionalNature, otherNature]
            },
        }
    }
</script>
