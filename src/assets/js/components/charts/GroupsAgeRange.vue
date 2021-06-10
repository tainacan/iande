<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Faixa etária dos grupos', 'iande') }}</h2>
        <ApexChart type="bar" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'GroupsAgeRangeChart',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            filterData () {
                return []
            },
            options () {
                return {}
            },
            series () {
                return []
            },
        },
    }
</script>