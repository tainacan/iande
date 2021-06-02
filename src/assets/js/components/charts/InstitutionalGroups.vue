<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Natureza dos grupos', 'iande') }}</h2>
        <ApexChart type="pie" :series="series" :options="options"/>
    </div>
</template>

<script>
    import ApexChart from 'vue-apexcharts'

    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'InstitutionalGroupsChart',
        components: {
            ApexChart,
        },
        props: {
            groups: { type: Array, required: true },
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

                for (const group of this.groups) {
                    if (!group.checkin_institutional) {
                        continue
                    } else if (group.checkin_institutional === 'yes') {
                        institutionalNature++
                    } else {
                        otherNature++
                    }
                }

                return [institutionalNature, otherNature]
            },
        },
    }
</script>
