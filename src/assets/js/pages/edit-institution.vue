<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="editInstitution">
                <CreateInstitution ref="form" :edit="editMode"/>

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
    import { call, sync } from 'vuex-pathify'

    import CreateInstitution from '@components/CreateInstitution.vue'
    import { api, qs } from '@utils'

    export default {
        name: 'EditInstitutionPage',
        components: {
            CreateInstitution,
        },
        data () {
            return {
                editMode: false,
                formError: '',
            }
        },
        computed: {
            institution: sync('institutions/current'),
        },
        async beforeMount () {
            if (qs.has('ID')) {
                try {
                    const institution = await api.get('institution/get', {
                        ID: Number(qs.get('ID'))
                    })
                    this.institution = { ...this.institution, ...institution }
                    this.editMode = true
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
            resetInstitution: call('institutions/reset'),
            async editInstitution () {
                this.formError = ''
                if (this.isFormValid()) {
                    try {
                        const verb = this.editMode ? 'update' : 'create'
                        const { ID, ...restInstitution } = this.institution
                        const result = await api.post(`institution/${verb}`, this.editMode ? this.institution : restInstitution)
                        await this.resetInstitution()
                        window.location.assign(this.$iandeUrl('institution/list'))
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
