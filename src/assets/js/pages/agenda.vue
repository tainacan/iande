<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Minha agenda</h1>
            <AppointmentsFilter id="time" label="Exibindo" :options="timeOptions" v-model="time"/>
            <GroupDetails v-for="group of filteredGroups" :key="group.ID" boxed :educators="educators" :group="group"/>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentsFilter from '../components/AppointmentsFilter.vue'
    import GroupDetails from '../components/GroupDetails.vue'
    import { api, constant, sortBy } from '../utils'

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
                const today = new Date().toISOString().slice(0, 10)
                if (this.time === 'next') {
                    return this.groups.filter(group => group.date >= today)
                } else {
                    return this.groups.filter(group => group.date < today)
                }
            },
            sortedGroups () {
                return this.sortedGroups.sort(sortBy(group => group.date, this.time === 'next'))
            },
            timeOptions: constant([
                { label: 'Próximas', value: 'next' },
                { label: 'Antigas', value: 'previous' },
            ]),
        },
        async created () {
            try {
                const [exhibitions, appointments, groups, educators] = await Promise.all([
                    api.get('exhibition/list'),
                    api.get('appointment/list_published'),
                    api.get('group/list_agenda'),
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
