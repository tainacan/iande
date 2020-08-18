<template>
    <article>
        <StepsIndicator :step="1"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="nextStep">
                <VisitDate ref="form" v-if="screen === 1"/>
                <ResponsiblePerson ref="form" v-else-if="screen === 2"/>
                <GroupNature ref="form" v-else-if="screen === 3" @add-institution="setScreen(4)"/>
                <CreateInstitution ref="form" v-else-if="screen === 4"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ formError }}</span>
                </div>

                <div class="iande-form-grid">
                    <button class="iande-button solid" type="button" v-if="screen > 1" @click="setScreen(screen - 1)">
                        <Icon icon="angle-left"/>
                        Voltar
                    </button>
                    <div v-else></div>
                    <button class="iande-button primary" type="submit">
                        {{ screen === 4 ? 'Salvar instituição' : 'Avançar' }}
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { call, get, sync } from 'vuex-pathify'

    import StepsIndicator from '../components/StepsIndicator.vue'
    import { api } from '../utils'

    // Lazy-loading candidates
    import CreateInstitution from '../components/CreateInstitution.vue'
    import GroupNature from '../components/GroupNature.vue'
    import ResponsiblePerson from '../components/ResponsiblePerson.vue'
    import VisitDate from '../components/VisitDate.vue'

    export default {
        name: 'CreateAppointmentPage',
        components: {
            CreateInstitution,
            GroupNature,
            Icon: FontAwesomeIcon,
            ResponsiblePerson,
            StepsIndicator,
            VisitDate,
        },
        data () {
            return {
                formError: '',
                screen: 1
            }
        },
        computed: {
            appointment: sync('appointments/current'),
            appointmentId: sync('appointments/current@ID'),
            appointmentInstitution: sync('appointments/current@institution'),
            fields: get('appointments/filteredFields'),
            institution: sync('institutions/current'),
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {
                    const appointment = await api.get('appointment/get', {
                        ID: Number(qs.get('ID'))
                    })
                    this.appointment = { ...this.appointment, ...appointment }
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            isFormValid () {
                const formComponent = this.$refs.form
                formComponent.$v.$touch()
                return !formComponent.$v.$invalid
            },
            async nextStep () {
                if (this.screen === 4) {
                    await this.saveInstitution()
                } else if (this.screen === 3 && this.appointment.institution != null) {
                    await this.saveAppointment()
                } else {
                    this.setScreen(this.screen + 1)
                }
            },
            resetAppointment: call('appointments/reset'),
            resetInstitution: call('institutions/reset'),
            async saveAppointment () {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        await api.post('appointment/update', this.fields)
                        await api.post('appointment/advance_step', { ID: this.appointmentId })
                        this.$refs.form.$v.$reset()
                        await this.resetInstitution()
                        await this.resetAppointment()
                        window.location.assign('../list')
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
            async saveInstitution () {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        const institution = await api.post('institution/create', this.institution)
                        this.appointmentInstitution = institution.ID
                        await this.saveAppointment()
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
            async setScreen (num) {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        const verb = this.fields.ID ? 'update' : 'create'
                        const result = await api.post(`appointment/${verb}`, this.fields)
                        this.appointmentId = result.ID
                        this.screen = num
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
