<template>
    <article>
        <StepsIndicator :step="2"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Informações do grupo</h1>
            <p>Nesta etapa você deve dar informações sobre o grupo que irá visitar o museu.</p>

            <form class="iande-form iande-stack stack-lg" @submit.prevent="confirmAppointment">
                <GroupsInfo ref="form" v-if="screen === 5"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ formError }}</span>
                </div>

                <div class="iande-form-grid">
                    <a class="iande-button solid" v-if="screen === 5" :href="`${iandeUrl}/appointment/list`">
                        <Icon icon="angle-left"/>
                        Voltar
                    </a>
                    <button class="iande-button solid" type="button" v-else @click="setScreen(5)">
                        <Icon icon="angle-left"/>
                        Voltar
                    </button>

                    <button class="iande-button primary" type="button" v-if="screen === 5" @click="setScreen(6)">
                        Avançar
                        <Icon icon="angle-right"/>
                    </button>
                    <button class="iande-button primary" v-else type="submit">
                        Avançar
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { get } from 'vuex-pathify'

    import StepsIndicator from '../components/StepsIndicator.vue'
    import { constant } from '../utils'

    // Lazy-loading candidates
    import GroupsInfo from '../components/GroupsInfo.vue'

    export default {
        name: 'ConfirmAppointmentPage',
        components: {
            GroupsInfo,
            Icon: FontAwesomeIcon,
            StepsIndicator,
        },
        data () {
            return {
                formError: '',
                screen: 5,
            }
        },
        computed: {
            fields: get('appointments/filteredFields'),
            iandeUrl: constant(window.IandeSettings.iandeUrl)
        },
        methods: {
            async confirmAppointment () {
                console.log(this.fields)
            },
            isFormValid () {
                const formComponent = this.$refs.form
                formComponent.$v.$touch()
                return !formComponent.$v.$invalid
            },
            async setScreen (num) {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        await api.post(`appointment/update`, this.fields)
                        this.screen = num
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
