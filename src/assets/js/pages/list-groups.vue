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
            <GroupsAgenda :exhibitions="exhibitions" :groups="groups" v-if="viewMode === 'calendar'"/>
            <template v-else>
                <AppointmentsFilter id="time" label="Exibindo" :options="timeOptions" v-model="time"/>
            </template>
        </div>
    </article>
</template>

<script>
    import AppointmentsFilter from '../components/AppointmentsFilter.vue'
    import GroupsAgenda from '../components/GroupsCalendar.vue'
    import { api, constant } from '../utils'

    export default {
        name: 'ListGroupsPage',
        components: {
            AppointmentsFilter,
            GroupsAgenda,
        },
        data () {
            return {
                exhibitions: [],
                groups: [],
                time: 'next',
                viewMode: 'calendar'
            }
        },
        computed: {
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
                const exhibitions = await api.get('exhibition/list')
                this.exhibitions = exhibitions
                const groups = await api.get('group/list') ?? []
                this.groups = groups
            } catch (err) {
                console.error(err)
            }
        }
    }
</script>
