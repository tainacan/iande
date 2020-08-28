<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Seus agendamentos</h1>
            <fieldset class="iande-appointments-filter iande-form" aria-labelledby="filters-label">
                <div class="iande-appointments-filter__row">
                    <div id="filters-label" class="iande-appointments-filter__label">Exibindo:</div>
                    <input id="filters-next" type="radio" name="filter" value="next" v-model="filter">
                    <label for="filters-next">
                        <span class="iande-label">Próximas</span>
                    </label>
                    <input id="filters-previous" type="radio" name="filter" value="previous" v-model="filter">
                    <label for="filters-previous">
                        <span class="iande-label">Antigas</span>
                    </label>
                </div>
            </fieldset>
            <AppointmentDetails v-for="(appointment, n) in filteredAppoitments" :key="appointment.ID" :appointment="appointment" :n="n + 1"/>
            <div class="iande-container narrow">
                <a class="iande-button outline mb-lg" :href="`${iandeUrl}/appointment/create`">
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
        data () {
            return {
                filter: 'next',
            }
        },
        computed: {
            appointments: sync('appointments/list'),
            filteredAppoitments () {
                const today = new Date().toISOString().slice(0, 10)
                if (this.filter === 'next') {
                    return this.appointments.filter(appointment => appointment.date >= today)
                } else {
                    return this.appointments.filter(appointment => appointment.date < today)
                }
            },
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