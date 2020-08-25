<template>
    <div class="iande-appointments-agenda">
        Agenda
    </div>
</template>

<script>
    import { api } from '../../utils'

    export default {
        name: 'AppointmentsAgenda',
        data () {
            return {
                appointments: [],
            }
        },
        computed: {
            appointmentsByDate () {
                const dates = {}
                for (const appointment of this.appointments) {
                    const date = dates[appointment.date]
                    if (date) {
                        date.push(appointment)
                    } else {
                        dates[appointment.date] = [appointment]
                    }
                }
                return dates
            }
        },
        async created () {
            try {
                const appointments = await api.post('appointment/list_published')
                this.appointments = appointments
            } catch (err) {
                console.error(err)
            }
        }
    }
</script>
