<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Seus agendamentos', 'iande') }}</h1>
            <div class="iande-appointments-toolbar">
                <AppointmentsFilter id="time" :label="__('Exibindo', 'iande')" :options="filterOptions" v-model="filter"/>
                <a class="iande-button small outline" :href="$iandeUrl('appointment/create')">
                    <Icon icon="circle-plus"/>
                    {{ __('Criar novo agendamento', 'iande') }}
                </a>
            </div>
            <AppointmentDetails v-for="appointment of filteredAppointments" :key="appointment.ID" :appointment="appointment"/>
            <div class="iande-container narrow" v-if="filteredAppointments.length > 0">
                <a class="iande-button outline" :href="$iandeUrl('appointment/create')">
                    <Icon icon="circle-plus"/>
                    {{ __('Criar novo agendamento', 'iande') }}
                </a>
            </div>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentDetails from '@components/AppointmentDetails.vue'
    import AppointmentsFilter from '@components/AppointmentsFilter.vue'
    import { __ } from '@plugins/wp-i18n'
    import { api, constant, sortBy, today } from '@utils'

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
                        .filter(appointment => appointment.groups?.some(group => group.date >= today))
                        .sort(sortBy(hourFn, true))
                } else {
                    return this.appointments
                        .filter(appointment => appointment.groups?.every(group => group.date < today))
                        .sort(sortBy(hourFn, false))
                }
            },
            filterOptions: constant([
                { label: __('Próximas', 'iande'), value: 'next' },
                { label: __('Antigas', 'iande'), value: 'previous' },
            ]),
            exhibitions: sync('exhibitions/list'),
            institutions: sync('institutions/list'),
        },
        async created () {
            const [appointments, exhibitions, institutions] = await Promise.all([
                api.get('appointment/list'),
                api.get('exhibition/list/?show_private=1'),
                api.get('institution/list_published'),
            ])
            this.appointments = appointments
            this.exhibitions = exhibitions
            this.institutions = institutions
        }
    }
</script>
