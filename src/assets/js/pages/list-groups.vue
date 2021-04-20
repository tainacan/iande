<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Calendário geral</h1>
            <AppointmentsFilter id="view" label="Modo de visualização" :options="viewModeOptions" v-model="viewMode"/>
            <div class="iande-groups-legend" aria-hidden="true">
                <div class="iande-groups-legend__label"><Icon icon="question-circle"/> Legenda da mediação:</div>
                <div class="iande-groups-legend__entry assigned-other">Com mediação atribuída</div>
                <div class="iande-groups-legend__entry unassigned">Sem mediação atribuída</div>
                <div class="iande-groups-legend__entry assigned-self">Mediação atribuída a você</div>
            </div>
            <GroupsAgenda :educators="educators" v-if="viewMode === 'calendar'"/>
            <template v-else>
                <AppointmentsFilter id="time" label="Exibindo" :options="timeOptions" v-model="time"/>
                <GroupDetails v-for="group of filteredGroups" :key="group.ID" boxed :educators="educators" :group="group"/>
            </template>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentsFilter from '../components/AppointmentsFilter.vue'
    import GroupsAgenda from '../components/GroupsCalendar.vue'
    import GroupDetails from '../components/GroupDetails.vue'
    import { api, constant, sortBy, today } from '../utils'

    export default {
        name: 'ListGroupsPage',
        components: {
            AppointmentsFilter,
            GroupsAgenda,
            GroupDetails,
        },
        data () {
            return {
                educators: [],
                time: 'next',
                viewMode: 'calendar',
            }
        },
        computed: {
            appointments: sync('appointments/list'),
            exhibitions: sync('exhibitions/list'),
            filteredGroups () {
                if (this.time === 'next') {
                    return this.groups.filter(group => group.date >= today)
                } else {
                    return this.groups.filter(group => group.date < today)
                }
            },
            groups: sync('groups/list'),
            sortedGroups () {
                return this.groups.sort(sortBy(group => group.date, this.time === 'next'))
            },
            timeOptions: constant([
                { label: 'Próximas', value: 'next' },
                { label: 'Antigas', value: 'previous' },
            ]),
            viewModeOptions: constant([
                { label: 'Calendário', icon: ['far', 'calendar'], value: 'calendar' },
                { label: 'Lista', icon: 'list', value: 'list' },
            ]),
        },
        async created () {
            try {
                const [exhibitions, appointments, groups, educators] = await Promise.all([
                    api.get('exhibition/list'),
                    api.get('appointment/list_published'),
                    api.get('group/list'),
                    api.get('user/list?cap=manage_iande_options'),
                ])
                this.exhibitions = exhibitions
                this.appointments = appointments
                this.groups = groups
                this.educators = educators
            } catch (err) {
                console.error(err)
            }
        },
    }
</script>
