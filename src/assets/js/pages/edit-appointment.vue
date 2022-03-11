<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="updateAppointment">
                <SelectExhibition ref="form" v-if="screen === 1"/>
                <GroupsDate ref="form" v-else-if="screen === 2"/>
                <GroupsAdditionalInfo ref="form" v-else-if="screen === 3"/>
                <SelectInstitution ref="form" v-else-if="screen === 4" :canAddInstitution="false"/>
                <AdditionalData ref="form" v-else-if="screen === 6"/>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ __(formError, 'iande') }}</span>
                </div>

                <button class="iande-button primary" type="submit">
                    {{ __('Salvar', 'iande') }}
                </button>
            </form>
        </div>
    </article>
</template>

<script>
    import { call, get, sync } from 'vuex-pathify'

    import { api, qs } from '@utils'

    const AdditionalData = () => import(/* webpackChunkName: 'additional-data-step' */ '@components/AdditionalData.vue')
    const GroupsAdditionalInfo = () => import(/* webpackChunkName: 'groups-additional-info-step' */ '@components/GroupsAdditionalInfo.vue')
    const GroupsDate = () => import(/* webpackChunkName: 'groups-date-step' */ '@components/GroupsDate.vue')
    const SelectInstitution = () => import(/* webpackChunkName: 'select-institution-step' */ '@components/SelectInstitution.vue')
    const SelectExhibition = () => import(/* webpackChunkName: 'select-exhibition-step' */ '@components/SelectExhibition.vue')

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
                        window.location.assign(this.$iandeUrl('appointment/list'))
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
