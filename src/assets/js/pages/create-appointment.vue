<template>
    <article class="mt-lg">
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

        <Modal ref="firstModal" :label="__('Sucesso!', 'iande')" narrow @close="listAppointments">
            <div class="iande-stack iande-form">
                <h1>{{ __('Preenchimento finalizado', 'iande') }}</h1>
                <p>{{ __('Agradecemos pelo seu tempo em completar todas as etapas do agendamento. Você pode revisar o agendamento ou já enviar a solicitação para o museu.', 'iande') }}</p>
                <div class="iande-form-grid">
                    <a class="iande-button solid" :href="$iandeUrl('appointment/list')">
                        {{ __('Revisar informações', 'iande') }}
                    </a>
                    <button class="iande-button primary" @click="finishAppointment">
                        {{ __('Finalizar', 'iande') }}
                    </button>
                </div>
            </div>
        </Modal>
        <AppointmentSuccessModal ref="secondModal"/>
    </article>
</template>

<script>
    import { call, get, sync } from 'vuex-pathify'

    import AppointmentSuccessModal from '@components/AppointmentSuccessModal.vue'
    import Modal from '@components/Modal.vue'
    import { api, qs } from '@utils'

    const AdditionalData = () => import(/* webpackChunkName: 'additional-data-step' */ '@components/AdditionalData.vue')
    const CreateInstitution = () => import(/* webpackChunkName: 'create-institution-step' */ '@components/CreateInstitution.vue')
    const GroupsAdditionalInfo = () => import(/* webpackChunkName: 'groups-additional-info-step' */ '@components/GroupsAdditionalInfo.vue')
    const GroupsDate = () => import(/* webpackChunkName: 'groups-date-step' */ '@components/GroupsDate.vue')
    const SelectInstitution = () => import(/* webpackChunkName: 'select-institution-step' */ '@components/SelectInstitution.vue')
    const SelectExhibition = () => import(/* webpackChunkName: 'select-exhibition-step' */ '@components/SelectExhibition.vue')

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
            action: 'saveAppointment',
            previous: 2,
            next: 5,
            validatePrevious: true,
        },
        4: {
            component: CreateInstitution,
            action: 'saveInstitution',
            previous: 3,
            next: 3,
            validatePrevious: false,
        },
        5: {
            component: GroupsAdditionalInfo,
            action: 'saveAppointment',
            previous: 3,
            next: 6,
            validatePrevious: true,
        },
        6: {
            component: AdditionalData,
            action: 'confirmAppointment',
            previous: 5,
            validatePrevious: true,
        },
    }

    export default {
        name: 'CreateAppointmentPage',
        components: {
            AppointmentSuccessModal,
            Modal,
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
            if (qs.has('screen')) {
                this.screen = Number(qs.get('screen'))
            }
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
            async confirmAppointment () {
                try {
                    if (await this.updateAppointment()) {
                        this.$refs.firstModal.open()
                        return true
                    } else {
                        return false
                    }
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
            async finishAppointment () {
                this.$refs.firstModal.close(false)
                await api.post('appointment/set_status', { ID: this.appointment.ID, post_status: 'pending' })
                this.$refs.secondModal.open()
            },
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
        }
    }
</script>
