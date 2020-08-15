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
                        {{ screen <= 4 ? 'Voltar' : 'Salvar instituição' }}
                    </button>
                    <div v-else></div>
                    <button class="iande-button primary" type="submit">
                        Avançar
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { get, sync } from 'vuex-pathify'

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
            appointmentId: sync('appointments/current@ID'),
            fields: get('appointments/filteredFields')
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {
                    const appointment = await api.get('appointment/get', {
                        ID: Number(qs.get('ID'))
                    })
                    this.$store.set('appointments/current', appointment)
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
            nextStep () {
                if (this.screen <= 4) {
                    this.setScreen(this.screen + 1)
                } else {
                    this.saveAppointment()
                }
            },
            saveAppointment () {
                // TODO
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
