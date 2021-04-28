<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Seus agendamentos', 'iande') }}</h1>
            <div class="iande-appointments-toolbar">
                <AppointmentsFilter id="time" :label="__('Exibindo', 'iande')" :options="filterOptions" v-model="filter"/>
                <a class="iande-button small outline" :href="$iandeUrl('appointment/create')" v-if="appointments.length > 0">
                    <Icon icon="plus-circle"/>
                    {{ __('Criar novo agendamento', 'iande') }}
                </a>
            </div>
            <AppointmentDetails v-for="appointment in filteredAppointments" :key="appointment.ID" :appointment="appointment"/>
            <div class="iande-container narrow">
                <a class="iande-button outline" :href="$iandeUrl('appointment/create')">
                    <Icon icon="plus-circle"/>
                    {{ __('Criar novo agendamento', 'iande') }}
                </a>
            </div>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentDetails from '../components/AppointmentDetails.vue'
    import AppointmentsFilter from '../components/AppointmentsFilter.vue'
    import { __ } from '../plugins/wp-i18n'
    import { api, constant, sortBy, today } from '../utils'

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
                const hourFn = appointment => {
                    const firstGroup = appointment.groups[0]
                    return `${firstGroup.date} ${firstGroup.hour}`
                }
                if (this.filter === 'next') {
                    return this.appointments
                        .filter(appointment => appointment.groups.some(group => group.date >= today))
                        .sort(sortBy(hourFn, true))
                } else {
                    return this.appointments
                        .filter(appointment => appointment.groups.every(group => group.date < today))
                        .sort(sortBy(hourFn, false))
                }
            },
            filterOptions: constant([
                { label: __('Próximas', 'iande'), value: 'next' },
                { label: __('Antigas', 'iande'), value: 'previous' },
            ]),
            institutions: sync('institutions/list'),
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