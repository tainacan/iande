<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="updateAppointment">
                <SelectExhibition ref="form" v-if="screen === 1"/>
                <GroupsDate ref="form" v-else-if="screen === 2"/>
                <SelectInstitution ref="form" v-else-if="screen === 3" :canAddInstitution="false"/>
                <GroupsAdditionalInfo ref="form" v-else-if="screen === 5"/>
                <AdditionalData ref="form" v-else-if="screen === 6"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ formError }}</span>
                </div>

                <button class="iande-button primary" type="submit">
                    Salvar
                </button>
            </form>
        </div>
    </article>
</template>

<script>
    import { call, get, sync } from 'vuex-pathify'

    import { api, normalizeLanguages } from '../utils'

    // Lazy-loading candidates
    import AdditionalData from '../components/AdditionalData.vue'
    import GroupsAdditionalInfo from '../components/GroupsAdditionalInfo.vue'
    import GroupsDate from '../components/GroupsDate.vue'
    import SelectInstitution from '../components/SelectInstitution.vue'
    import SelectExhibition from '../components/SelectExhibition.vue'

    export default {
        name: 'EditAppointmentPage',
        components: {
            AdditionalData,
            GroupsAdditionalInfo,
            GroupsDate,
            SelectExhibition,
            SelectInstitution,
        },
        data () {
            return {
                formError: '',
                screen: 1,
            }
        },
        computed: {
            appointment: sync('appointments/current'),
            fields: get('appointments/filteredFields'),
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
            resetAppointment: call('appointments/reset'),
            async updateAppointment () {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        const result = await api.post('appointment/update', this.fields)
                        await this.resetAppointment()
                        window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
