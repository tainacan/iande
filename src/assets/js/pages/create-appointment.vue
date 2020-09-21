<template>
    <article>
        <StepsIndicator :step="1"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="nextStep">
                <component :is="route.component" ref="form" @add-institution="setScreen(3)"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ formError }}</span>
                </div>

                <div class="iande-form-grid">
                    <button class="iande-button solid" type="button" v-if="route.previous" @click="previousStep">
                        <Icon icon="angle-left"/>
                        Voltar
                    </button>
                    <div v-else></div>

                    <button class="iande-button primary" type="submit">
                        Avançar
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>
        <Modal ref="modal" @close="listAppointments">
            <div class="iande-stack iande-form">
                <h1>Reserva enviada com sucesso!</h1>
                <p>Uma reserva de data e horário foi enviada ao museu, mas para garantir o agendamento é necessário completar formulário com mais informações.</p>
                <div class="iande-form-grid">
                    <a class="iande-button solid" :href="`${iandeUrl}/appointment/list`">
                        Ver agendamentos
                    </a>
                    <a class="iande-button primary" :href="`${iandeUrl}/appointment/confirm?ID=${appointmentId}`">
                        Completar reserva
                    </a>
                </div>
            </div>
        </Modal>
    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { call, get, sync } from 'vuex-pathify'

    import Modal from '../components/Modal.vue'
    import StepsIndicator from '../components/StepsIndicator.vue'
    import { api, constant, normalizeLanguages } from '../utils'

    // Lazy-loading candidates
    import CreateInstitution from '../components/CreateInstitution.vue'
    import SelectInstitution from '../components/SelectInstitution.vue'
    import VisitDate from '../components/VisitDate.vue'

    const routes = {
        1: {
            component: VisitDate,
            action: 'saveAppointment',
            next: 2,
        },
        2: {
            component: SelectInstitution,
            action: 'submitAppointment',
            previous: 1,
        },
        3: {
            component: CreateInstitution,
            action: 'saveInstitution',
            previous: 2,
            next: 2,
        },
    }

    export default {
        name: 'CreateAppointmentPage',
        components: {
            Icon: FontAwesomeIcon,
            Modal,
            StepsIndicator,
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
            appointmentInstitution: sync('appointments/current@institution_id'),
            fields: get('appointments/filteredFields'),
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            institution: sync('institutions/current'),
            institutions: sync('institutions/list'),
            route () {
                return routes[this.screen]
            },
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {
                    const appointment = await api.get('appointment/get', {
                        ID: Number(qs.get('ID'))
                    })
                    this.appointment = normalizeLanguages({ ...this.appointment, ...appointment })
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
            listAppointments () {
                window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
            },
            async nextStep () {
                this.formError = ''
                if (this.isFormValid()) {
                    if (this[this.route.action]() && this.route.next) {
                        this.setScreen(this.route.next)
                    }
                }
            },
            previousStep () {
                this.formError = ''
                if (this.route.previous) {
                    this.setScreen(this.route.previous)
                }
            },
            resetAppointment: call('appointments/reset'),
            resetInstitution: call('institutions/reset'),
            async saveAppointment () {
                try {
                    const verb = this.fields.ID ? 'update' : 'create'
                    const result = await api.post(`appointment/${verb}`, this.fields)
                    this.appointmentId = result.ID
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
            async saveInstitution () {
                try {
                    const institution = await api.post('institution/create', this.institution)
                    this.institutions = [...this.institutions, institution]
                    this.appointmentInstitution = institution.ID
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
            setScreen (num) {
                this.screen = num
            },
            async submitAppointment () {
                try {
                    await api.post('appointment/update', this.fields)
                    await api.post('appointment/advance_step', { ID: this.appointmentId })
                    this.$refs.form.$v.$reset()
                    await this.resetInstitution()
                    this.$refs.modal.open()
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
        }
    }
</script>
