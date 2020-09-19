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

    const routes = {
        4: {
            component: GroupsAdditionalInfo,
            action: 'updateAppointment',
            next: 5,
        },
        5: {
            component: AdditionalData,
            action: 'confirmAppointment',
            previous: 4,
        },
    }

    export default {
        name: 'ConfirmAppointmentPage',
        components: {
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
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
            isFormValid () {
                const formComponent = this.$refs.form
                formComponent.$v.$touch()
                return !formComponent.$v.$invalid
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
                    await api.post('appointment/update', this.fields)
                    return true
                } catch (err) {
                    this.formError = err
                    return false
                }
            },
        }
    }
</script>
