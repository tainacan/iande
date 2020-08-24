<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Seus agendamentos</h1>
            <AppointmentDetails v-for="(appointment, n) in appointments" :key="appointment.ID" :appointment="appointment" :n="n + 1"/>
            <div class="iande-container narrow">
                <a class="iande-button outline" :href="`${iandeUrl}/appointment/create`">
                    <Icon icon="plus-circle"/>
                    Criar novo agendamento
                </a>
            </div>
        </div>

    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { sync } from 'vuex-pathify'

    import AppointmentDetails from '../components/AppointmentDetails'
    import { api, constant } from '../utils'

    export default {
        name: 'ListAppointmentsPage',
        components: {
            AppointmentDetails,
            Icon: FontAwesomeIcon,
        },
        computed: {
            appointments: sync('appointments/list'),
            iandeUrl: constant(window.IandeSettings.iandeUrl),
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