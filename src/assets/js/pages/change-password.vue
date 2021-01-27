<template>
    <article id="iande-edit-user" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Alteração de senha</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="changePassword">
                <div>
                    <label class="iande-label" for="password">Nova senha</label>
                    <Input id="password" type="password" v-model="password" :validations="$v.password"/>
                </div>
                <div>
                    <label class="iande-label" for="confirmPassword">Confirmar senha</label>
                    <Input id="confirmPassword" type="password" v-model="confirmPassword" :validations="$v.confirmPassword"/>
                </div>
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
    import { minLength, required, sameAs } from 'vuelidate/lib/validators'
    import { get } from 'vuex-pathify'

    import Input from '../components/Input.vue'
    import { api } from '../utils'

    export default {
        name: 'ChangePasswordPage',
        components: {
            Input,
        },
        data () {
            return {
                confirmPassword: '',
                formError: '',
                password: '',
            }
        },
        computed: {
            user: get('user/user'),
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
                        const user = await api.post('user/edit', payload)
                        await api.post('user/logout')
                        window.location.assign(`${window.IandeSettings.iandeUrl}/user/login`)
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>