<template>
    <article>
        <div class="iande-container iande-stack stack-lg">
            <h1>Seus agendamentos</h1>
            <AppointmentDetails v-for="(appointment, n) in appointments" :key="appointment.ID" :appointment="appointment" :n="n + 1"/>
        </div>
    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import AppointmentDetails from '../components/AppointmentDetails'
    import { api } from '../utils'

    export default {
        name: 'ListAppointmentsPage',
        components: {
            AppointmentDetails,
        },
        computed: {
            appointments: sync('appointments/list'),
        },
        async created () {
            if (this.appointments.length === 0) {
                const appointments = await api.get('appointment/list')
                this.appointments = appointments
            }
        }
    }
</script>