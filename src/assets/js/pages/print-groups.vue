<template>
    <article class="iande-print-page">
        <table class="iande-print-table">
            <thead>
                <tr>
                    <th v-if="exhibitions.length > 1">{{ __('Exposição', 'iande') }}</th>
                    <th>{{ __('Horário', 'iande') }}</th>
                    <th>{{ __('Agendamento', 'iande') }}</th>
                    <th>{{ __('Grupo', 'iande') }}</th>
                    <th>{{ __('Instituição', 'iande') }}</th>
                    <th>{{ __('Contato', 'iande') }}</th>
                    <th>{{ __('Educador', 'iande') }}</th>
                </tr>
            </thead>
            <tbody>
                <PrintGroupsRow v-for="group of sortedGroups" :key="group.ID" :appointments="appointmentsMap" :educators="educatorsMap" :exhibitions="exhibitions" :group="group" :institutions="institutionsMap"/>
            </tbody>
        </table>
    </article>
</template>

<script>
    import { DateTime } from 'luxon'

    import PrintGroupsRow from '@components/PrintGroupsRow.vue'
    import { api, arrayToMap, sortBy } from '@utils'

    export default {
        name: 'PrintGroupsPage',
        components: {
            PrintGroupsRow,
        },
        data () {
            return {
                appointments: [],
                educators: [],
                exhibitions: [],
                groups: [],
                institutions: [],
                monday: null,
            }
        },
        computed: {
            appointmentsMap () {
                return arrayToMap(this.appointments, 'ID')
            },
            educatorsMap () {
                return arrayToMap(this.educators, 'ID')
            },
            institutionsMap () {
                return arrayToMap(this.institutions, 'ID')
            },
            sortedGroups () {
                if (!this.monday) {
                    return []
                }
                const filteredGroups = this.groups.filter(group => {
                    return group.date >= this.monday
                })
                return filteredGroups.sort(sortBy(group => group.date))
            },
        },
        async created () {
            try {
                this.computeDates()

                const [exhibitions, appointments, groups, educators, institutions] = await Promise.all([
                    api.get('exhibition/list/?show_private=1'),
                    api.get('appointment/list_published'),
                    api.get('group/list'),
                    api.get('user/list/?cap=checkin'),
                    api.get('institution/list'),
                ])
                this.exhibitions = exhibitions
                this.appointments = appointments
                this.groups = groups
                this.educators = educators
                this.institutions = institutions

                this.$nextTick(() => {
                    window.print()
                })
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            computeDates () {
                const thisMonday = DateTime.now().startOf('week')
                this.monday = thisMonday.toISODate()
            },
        }
    }
</script>
