<template>
    <article>
        <StepsIndicator :step="1"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="nextStep">
                <VisitDate ref="form" v-if="screen === 1"/>
                <ResponsiblePerson ref="form" v-else-if="screen === 2"/>
                <GroupNature ref="form" v-else-if="screen === 3"/>

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
    import { get } from 'vuex-pathify'

    import StepsIndicator from '../components/StepsIndicator.vue'

    // Lazy-loading candidates
    import GroupNature from '../components/GroupNature.vue'
    import ResponsiblePerson from '../components/ResponsiblePerson.vue'
    import VisitDate from '../components/VisitDate.vue'

    export default {
        name: 'CreateAppointmentPage',
        components: {
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
            fields: get('appointments/filteredFields')
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
            setScreen (num) {
                this.formError = ''
                if (this.isFormValid()) {
                    this.screen = num
                }
            },
        }
    }
</script>
