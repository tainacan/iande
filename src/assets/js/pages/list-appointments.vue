<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Seus agendamentos</h1>
            <div class="iande-appointments-toolbar">
                <AppointmentsFilter id="time" label="Exibindo" :options="filterOptions" v-model="filter"/>
                <a class="iande-button small outline" :href="$iandeUrl('appointment/create')" v-if="appointments.length > 0">
                    <Icon icon="plus-circle"/>
                    Criar novo agendamento
                </a>
            </div>
            <AppointmentDetails v-for="appointment in filteredAppointments" :key="appointment.ID" :appointment="appointment"/>
            <div class="iande-container narrow">
                <a class="iande-button outline" :href="$iandeUrl('appointment/create')">
                    <Icon icon="plus-circle"/>
                    Criar novo agendamento
                </a>
            </div>
        </div>

    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentDetails from '../components/AppointmentDetails.vue'
    import AppointmentsFilter from '../components/AppointmentsFilter.vue'
    import { api, constant, sortBy } from '../utils'

    export default {
        name: 'ListAppointmentsPage',
        components: {
            AppointmentDetails,
            AppointmentsFilter,
        },
        data () {
            return {
                filter: 'next',
            }
        },
        computed: {
            appointments: sync('appointments/list'),
            filteredAppointments () {
                const today = new Date().toISOString().slice(0, 10)
                if (this.filter === 'next') {
                    return this.sortedAppointments.filter(appointment => {
                        return appointment.groups.some(group => group.date >= today)
                    })
                } else {
                    return this.sortedAppointments.filter(appointment => {
                        return appointment.groups.every(group => group.date < today)
                    })
                }
            },
            filterOptions: constant([
                { label: 'Próximas', value: 'next' },
                { label: 'Antigas', value: 'previous' },
            ]),
            institutions: sync('institutions/list'),
            sortedAppointments () {
                return this.appointments.sort(sortBy(appointment => appointment.date))
            },
        },
        async created () {
            if (this.appointments.length === 0) {
                const appointments = await api.get('appointment/list')
                this.appointments = appointments
            }
            if (this.institutions.length === 0) {
                const institutions = await api.get('institution/list')
                this.institutions = institutions
            }
        }
    }
</script>