<template>
    <article>
        <StepsIndicator :step="2"/>

        <div class="iande-container narrow">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="nextStep">
                <component :is="route.component" ref="form"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ formError }}</span>
                </div>

                <div class="iande-form-grid">
                    <button class="iande-button solid" type="button" v-if="route.previous" @click="previousStep">
                        <Icon icon="angle-left"/>
                        Voltar
                    </button>
                    <a class="iande-button solid" v-else :href="`${iandeUrl}/appointment/list`">
                        <Icon icon="angle-left"/>
                        Voltar
                    </a>

                    <button class="iande-button primary" type="submit">
                        Avançar
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>
        <Modal ref="firstModal" @close="listAppointments">
            <div class="iande-stack iande-form">
                <h1>Preenchimento finalizado</h1>
                <p>Agradecemos pelo seu tempo em completar detalhadamente todas as etapas do agendamento. Você pode revisar o agendamento ou já enviar a solicitação para o museu.</p>
                <div class="iande-form-grid">
                    <a class="iande-button solid" :href="`${iandeUrl}/appointment/list`">
                        Revisar informações
                    </a>
                    <button class="iande-button primary" @click="finishAppointment">
                        Finalizar
                    </button>
                </div>
            </div>
        </Modal>
        <AppointmentSuccessModal ref="secondModal"/>
    </article>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { get, sync } from 'vuex-pathify'

    import AppointmentSuccessModal from '../components/AppointmentSuccessModal.vue'
    import Modal from '../components/Modal.vue'
    import StepsIndicator from '../components/StepsIndicator.vue'
    import { api, constant } from '../utils'

    // Lazy-loading candidates
    import AdditionalData from '../components/AdditionalData.vue'
    import GroupsAdditionalInfo from '../components/GroupsAdditionalInfo.vue'

    const routes = {
        5: {
            component: GroupsAdditionalInfo,
            action: 'updateAppointment',
            next: 6,
        },
        6: {
            component: AdditionalData,
            action: 'confirmAppointment',
            previous: 5,
        },
    }

    export default {
        name: 'ConfirmAppointmentPage',
        components: {
            AppointmentSuccessModal,
            Icon: FontAwesomeIcon,
            Modal,
            StepsIndicator,
        },
        data () {
            return {
                formError: '',
                screen: 5,
            }
        },
        computed: {
            appointment: sync('appointments/current'),
            fields: get('appointments/filteredFields'),
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            route () {
                return routes[this.screen]
            },
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
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
                    const appointment = await api.post('appointment/update', this.fields)
                    this.appointment = { ...this.appointment, ...appointment }
                    await api.post('appointment/advance_step', { ID: this.fields.ID })
                    this.$refs.firstModal.open()
                    return true
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
            setScreen (num) {
                this.screen = num
            },
            async updateAppointment (num) {
                try {
                    const appointment = await api.post('appointment/update', this.fields)
                    this.appointment = { ...this.appointment, ...appointment }
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
        }
    }
</script>
