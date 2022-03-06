<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Minha agenda', 'iande') }}</h1>
            <AppointmentsFilter id="time" :label="__('Exibindo', 'iande')" :options="timeOptions" v-model="time"/>
            <GroupDetails v-for="group of filteredGroups" :key="group.ID" boxed :educators="educators" :group="group"/>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentsFilter from '@components/AppointmentsFilter.vue'
    import GroupDetails from '@components/GroupDetails.vue'
    import { __ } from '@plugins/wp-i18n'
    import { api, constant, sortBy, today } from '@utils'

    export default {
        name: 'GroupsAgendaPage',
        components: {
            AppointmentsFilter,
            GroupDetails,
        },
        data () {
            return {
                educator: [],
                groups: [],
                time: 'next',
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
            timeOptions: constant([
                { label: __('Próximas', 'iande'), value: 'next' },
                { label: __('Antigas', 'iande'), value: 'previous' },
            ]),
        },
        async created () {
            try {
                const [exhibitions, appointments, groups, educators] = await Promise.all([
                    api.get('exhibition/list/?show_private=1'),
                    api.get('appointment/list_published'),
                    api.get('group/list_agenda'),
                    api.get('user/list/?cap=checkin'),
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
