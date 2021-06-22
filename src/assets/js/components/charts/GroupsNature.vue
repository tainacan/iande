<template>
    <div class="iande-chart-wrapper">
        <h2>{{ __('Natureza dos grupos', 'iande') }}</h2>
        <ApexChart type="pie" :series="series" :options="options"/>
    </div>
</template>

<script>
    import { __, _x } from '@plugins/wp-i18n'

    export default {
        name: 'GroupsNatureChart',
        props: {
            appointments: { type: Object, required: true },
            groups: { type: Array, required: true },
        },
        computed: {
            options () {
                return {
                    colors: ['#A8DBBC', '#7DB6C5'],
                    labels: [__('Grupo Institucional', 'iande'), _x('Outra', 'group', 'iande')],
                }
            },
            series () {
                let institutionalNature = 0
                let otherNature = 0

                for (const group of this.groups) {
                    const institutional = this.isInstitutional(group)

                    if (institutional === 'yes') {
                        institutionalNature++
                    } else if (institutional === 'no') {
                        otherNature++
                    }
                }

                return [institutionalNature, otherNature]
            },
        },
        methods: {
            isInstitutional (group) {
                if (group.checkin_institutional) {
                    return group.checkin_institutional
                } else {
                    const appointmentId = group.appointment_id
                    if (!appointmentId) {
                        return null
                    }

                    const nature = this.appointments[appointmentId].group_nature
                    if (nature) {
                        return nature === 'institutional' ? 'yes' : 'no'
                    } else {
                        return null
                    }
                }
            },
        },
    }
</script>
