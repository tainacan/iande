<template>
    <div class="iande-appointments-agenda">
        <Calendar activeView="month" :disableViews="['years', 'year']" locale="pt-br"/>
    </div>
</template>

<script>
    import Calendar from 'vue-cal'

    import 'vue-cal/dist/i18n/pt-br';

    import { api } from '../../utils'

    export default {
        name: 'AppointmentsAgenda',
        components: {
            Calendar,
        },
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
