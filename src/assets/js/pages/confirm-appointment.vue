<template>
    <article>
        <StepsIndicator :step="2"/>

        <div class="iande-container narrow">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="confirmAppointment">
                <GroupsAdditionalInfo ref="form" v-if="screen === 4"/>
                <AdditionalData ref="form" v-else-if="screen === 5"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ formError }}</span>
                </div>

                <div class="iande-form-grid">
                    <a class="iande-button solid" v-if="screen === 4" :href="`${iandeUrl}/appointment/list`">
                        <Icon icon="angle-left"/>
                        Voltar
                    </a>
                    <button class="iande-button solid" type="button" v-else @click="setScreen(4)">
                        <Icon icon="angle-left"/>
                        Voltar
                    </button>

                    <button class="iande-button primary" type="button" v-if="screen === 4" @click="saveAndSetScreen(5)">
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
    import { get, sync } from 'vuex-pathify'

    import StepsIndicator from '../components/StepsIndicator.vue'
    import { api, constant, normalizeLanguages } from '../utils'

    // Lazy-loading candidates
    import AdditionalData from '../components/AdditionalData.vue'
    import GroupsAdditionalInfo from '../components/GroupsAdditionalInfo.vue'

    export default {
        name: 'ConfirmAppointmentPage',
        components: {
            AdditionalData,
            GroupsAdditionalInfo,
            Icon: FontAwesomeIcon,
            StepsIndicator,
        },
        data () {
            return {
                formError: '',
                screen: 4,
            }
        },
        computed: {
            appointment: sync('appointments/current'),
            fields: get('appointments/filteredFields'),
            groupList: sync('appointments/current@group_list'),
            iandeUrl: constant(window.IandeSettings.iandeUrl)
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
                    if (!this.groupList) {
                        this.groupList = { groups: [] }
                    }
                    this.appointment = normalizeLanguages(this.appointment)
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            async confirmAppointment () {
                try {
                    await api.post('appointment/update', this.fields)
                    await api.post('appointment/advance_step', { ID: this.fields.ID })
                    window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
                } catch (err) {
                    this.formError = err
                }
            },
            isFormValid () {
                const formComponent = this.$refs.form
                formComponent.$v.$touch()
                return !formComponent.$v.$invalid
            },
            setScreen (num) {
                this.screen = num
            },
            async saveAndSetScreen (num) {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        await api.post('appointment/update', this.fields)
                        this.setScreen(num)
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
