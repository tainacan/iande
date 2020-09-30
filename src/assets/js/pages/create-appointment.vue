<template>
    <article>
        <StepsIndicator :step="1"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="nextStep">
                <component :is="route.component" ref="form" @add-institution="setScreen(4)"/>

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
    import { api, constant } from '../utils'

    // Lazy-loading candidates
    import CreateInstitution from '../components/CreateInstitution.vue'
    import GroupsDate from '../components/GroupsDate.vue'
    import SelectInstitution from '../components/SelectInstitution.vue'
    import SelectExhibition from '../components/SelectExhibition.vue'

    const routes = {
        1: {
            component: SelectExhibition,
            action: 'saveAppointment',
            next: 2,
        },
        2: {
            component: GroupsDate,
            action: 'saveAppointment',
            previous: 1,
            next: 3,
        },
        3: {
            component: SelectInstitution,
            action: 'submitAppointment',
            previous: 2,
        },
        4: {
            component: CreateInstitution,
            action: 'saveInstitution',
            previous: 3,
            next: 3,
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
                screen: 1,
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
            listAppointments () {
                window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
            },
            async nextStep () {
                this.formError = ''
                if (this.isFormValid()) {
                    const success = await this[this.route.action]()
                    if (success && this.route.next) {
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
                    this.appointment = { ...this.appointment, ...result }
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
                    const appointment = await api.post('appointment/update', this.fields)
                    this.appointment = { ...this.appointment, ...appointment }
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
