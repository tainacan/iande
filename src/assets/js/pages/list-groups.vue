<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Calendário geral', 'iande') }}</h1>
            <div class="iande-appointments-toolbar">
                <AppointmentsFilter id="view" :label="__('Modo de visualização', 'iande')" :options="viewModeOptions" v-model="viewMode"/>
                <a class="iande-button small outline" :href="$iandeUrl('group/print')" target="_blank">
                    <Icon icon="print"/>
                    {{ __('Imprimir', 'iande') }}
                </a>
            </div>
            <div class="iande-groups-legend" aria-hidden="true">
                <div class="iande-groups-legend__label"><Icon icon="circle-info"/> {{ __('Legenda da mediação:', 'iande') }}</div>
                <div class="iande-groups-legend__entry assigned-other">{{ __('Com mediação atribuída', 'iande') }}</div>
                <div class="iande-groups-legend__entry unassigned">{{ __('Sem mediação atribuída', 'iande') }}</div>
                <div class="iande-groups-legend__entry assigned-self">{{ __('Mediação atribuída a você', 'iande') }}</div>
            </div>
            <GroupsAgenda :educators="educators" v-if="viewMode === 'calendar'"/>
            <template v-else>
                <AppointmentsFilter id="time" :label="__('Exibindo', 'iande')" :options="timeOptions" v-model="time"/>
                <GroupDetails v-for="group of filteredGroups" :key="group.ID" boxed :educators="educators" :group="group"/>
            </template>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentsFilter from '@components/AppointmentsFilter.vue'
    import GroupsAgenda from '@components/GroupsCalendar.vue'
    import GroupDetails from '@components/GroupDetails.vue'
    import { __ } from '@plugins/wp-i18n'
    import { api, constant, sortBy, today } from '@utils'

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
                const hourFn = group => `${group.date} ${group.hour}`
                if (this.time === 'next') {
                    return this.groups.filter(group => group.date >= today).sort(sortBy(hourFn, true))
                } else {
                    return this.groups.filter(group => group.date < today).sort(sortBy(hourFn, false))
                }
            },
            groups: sync('groups/list'),
            institutions: sync('institutions/list'),
            timeOptions: constant([
                { label: __('Próximas', 'iande'), value: 'next' },
                { label: __('Antigas', 'iande'), value: 'previous' },
            ]),
            viewModeOptions: constant([
                { label: __('Calendário', 'iande'), icon: ['far', 'calendar'], value: 'calendar' },
                { label: __('Lista', 'iande'), icon: 'list-ul', value: 'list' },
            ]),
        },
        async created () {
            try {
                const [appointments, exhibitions, groups, institutions, educators] = await Promise.all([
                    api.get('appointment/list_published'),
                    api.get('exhibition/list/?show_private=1'),
                    api.get('group/list'),
                    api.get('institution/list_published'),
                    api.get('user/list/?cap=checkin'),
                ])
                this.appointments = appointments
                this.educators = educators
                this.exhibitions = exhibitions
                this.groups = groups
                this.institutions = institutions
            } catch (err) {
                console.error(err)
            }
        },
    }
</script>
