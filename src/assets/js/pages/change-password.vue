<template>
    <article id="iande-edit-user" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>{{ __('Alteração de senha', 'iande') }}</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="changePassword">
                <div>
                    <Label for="password">{{ __('Nova senha', 'iande') }}</Label>
                    <Input id="password" type="password" autocomplete="new-password" v-model="password" :v="$v.password"/>
                </div>
                <div>
                    <Label for="confirmPassword">{{ __('Confirmar senha', 'iande') }}</Label>
                    <Input id="confirmPassword" type="password" autocomplete="new-password" v-model="confirmPassword" :v="$v.confirmPassword"/>
                </div>
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
    import { minLength, required, sameAs } from 'vuelidate/lib/validators'
    import { get } from 'vuex-pathify'

    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import { api } from '@utils'

    export default {
        name: 'ChangePasswordPage',
        components: {
            Input,
            Label,
        },
        data () {
            return {
                confirmPassword: '',
                formError: '',
                password: '',
            }
        },
        computed: {
            user: get('users/current'),
        },
        validations: {
            confirmPassword: { minChar: minLength(6), required, samePassword: sameAs('password') },
            password: { minChar: minLength(6), required },
        },
        methods: {
            async changePassword () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        const payload = {
                            password: this.password,
                        }
                        const user = await api.post('user/change-password', payload)
                        await api.post('user/logout')
                        window.location.assign(this.$iandeUrl('user/login'))
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>
