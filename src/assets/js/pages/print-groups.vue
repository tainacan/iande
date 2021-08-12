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
                <PrintGroupsRow v-for="group of sortedGroups" :key="group.ID" :educators="educators" :exhibitions="exhibitions" :group="group"/>
            </tbody>
        </table>
    </article>
</template>

<script>
    import { DateTime } from 'luxon'
    import { sync } from 'vuex-pathify'

    import PrintGroupsRow from '@components/PrintGroupsRow.vue'
    import { api, sortBy } from '@utils'

    export default {
        name: 'PrintGroupsPage',
        components: {
            PrintGroupsRow,
        },
        data () {
            return {
                educators: [],
                exhibitions: [],
                nextMonday: null,
                thisMonday: null,
                twoWeeks: null,
            }
        },
        computed: {
            appointments: sync('appointments/list'),
            groups: sync('groups/list'),
            institutions: sync('institutions/list'),
            sortedGroups () {
                const { thisMonday, twoWeeks } = this
                if (!thisMonday) {
                    return []
                }
                const filteredGroups = this.groups.filter(group => {
                    return group.date >= thisMonday && group.date < twoWeeks
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
                    api.get('user/list/?cap=manage_iande_options'),
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
                this.thisMonday = thisMonday.toISODate()
                this.nextMonday = thisMonday.plus({ week: 1 }).toISODate()
                this.twoWeeks = thisMonday.plus({ week: 2 }).toISODate()
            },
        }
    }
</script>
