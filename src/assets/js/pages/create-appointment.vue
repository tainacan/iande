<template>
    <article>
        <StepsIndicator :step="1"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="nextStep">
                <component :is="route.component" ref="form" @add-institution="setScreen(4)"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ __(formError, 'iande') }}</span>
                </div>

                <div class="iande-form-grid">
                    <button class="iande-button solid" type="button" v-if="route.previous" @click="previousStep">
                        <Icon icon="angle-left"/>
                        {{ __('Voltar', 'iande') }}
                    </button>
                    <div v-else></div>

                    <button class="iande-button primary" type="submit">
                        {{ __('Avançar', 'iande') }}
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>

        <AppointmentWelcomeModal ref="welcomeModal"/>

        <Modal ref="successModal" :label="__('Sucesso!', 'iande')" narrow @close="listAppointments">
            <div class="iande-stack iande-form">
                <h1>{{ __('Reserva enviada com sucesso!', 'iande') }}</h1>
                <p>{{ __('Uma reserva de data e horário foi enviada ao museu, mas para garantir o agendamento é necessário completar formulário com mais informações.', 'iande') }}</p>
                <div class="iande-form-grid">
                    <a class="iande-button solid" :href="$iandeUrl('appointment/list')">
                        {{ __('Ver agendamentos', 'iande') }}
                    </a>
                    <a class="iande-button primary" :href="$iandeUrl(`appointment/confirm?ID=${appointmentId}`)">
                        {{ __('Completar reserva', 'iande') }}
                    </a>
                </div>
            </div>
        </Modal>
    </article>
</template>

<script>
    import { call, get, sync } from 'vuex-pathify'

    import AppointmentWelcomeModal from '../components/AppointmentWelcomeModal.vue'
    import Modal from '../components/Modal.vue'
    import StepsIndicator from '../components/StepsIndicator.vue'
    import { api } from '../utils'

    const CreateInstitution = () => import(/* webpackChunkName: 'create-institution-step' */ '../components/CreateInstitution.vue')
    const GroupsDate = () => import(/* webpackChunkName: 'groups-date-step' */ '../components/GroupsDate.vue')
    const SelectInstitution = () => import(/* webpackChunkName: 'select-institution-step' */ '../components/SelectInstitution.vue')
    const SelectExhibition = () => import(/* webpackChunkName: 'select-exhibition-step' */ '../components/SelectExhibition.vue')

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
            validatePrevious: true,
        },
        3: {
            component: SelectInstitution,
            action: 'submitAppointment',
            previous: 2,
            validatePrevious: true,
        },
        4: {
            component: CreateInstitution,
            action: 'saveInstitution',
            previous: 3,
            next: 3,
            validatePrevious: false,
        },
    }

    export default {
        name: 'CreateAppointmentPage',
        components: {
            AppointmentWelcomeModal,
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
        mounted () {
            try {
                const modalShown = window.localStorage.getItem('iande.appointmentWelcome')
                if (!modalShown) {
                    this.$nextTick(() => {
                        this.$refs.welcomeModal.open()
                        window.localStorage.setItem('iande.appointmentWelcome', 'shown')
                    })
                }
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            isFormValid () {
                const formComponent = this.$refs.form
                formComponent.$v.$touch()
                return !formComponent.$v.$invalid
            },
            listAppointments () {
                window.location.assign(this.$iandeUrl('appointment/list'))
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
                if (!this.route.validatePrevious || this.isFormValid()) {
                    this.setScreen(this.route.previous)
                }
            },
            resetAppointment: call('appointments/reset'),
            resetInstitution: call('institutions/reset'),
            async saveAppointment () {
                try {
                    const verb = this.fields.ID ? 'update' : 'create'
                    const appointment = await api.post(`appointment/${verb}`, this.fields)
                    this.appointment = { ...this.appointment, ...appointment }
                    this.appointmentId = appointment.ID
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
                    this.$refs.successModal.open()
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
        }
    }
</script>
