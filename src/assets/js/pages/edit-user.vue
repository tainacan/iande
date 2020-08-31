<template>
    <article id="iande-edit-user" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Edição de usuário</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="updateUser">
                <div>
                    <label class="iande-label" for="firstName">Nome</label>
                    <Input id="firstName" type="text" v-model="firstName" :validations="$v.firstName"/>
                </div>
                <div>
                    <label class="iande-label" for="lastName">Sobrenome</label>
                    <Input id="lastName" type="text" v-model="lastName" :validations="$v.lastName"/>
                </div>
                <div>
                    <label class="iande-label" for="email">E-mail</label>
                    <Input id="email" type="email" v-model="email" :validations="$v.email"/>
                </div>
                <div>
                    <label class="iande-label" for="phone">Telefone</label>
                    <MaskedInput id="phone" type="tel" :mask="phoneMask" placeholder="DDD + Telefone" v-model="phone" :validations="$v.phone"/>
                </div>
                <p class="iande-password-notice">
                    Deixe os campos a seguir vazios para manter a senha atual
                </p>
                <div>
                    <label class="iande-label" for="password">Senha</label>
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
    import { email, minLength, required, sameAs } from 'vuelidate/lib/validators'
    import { get } from 'vuex-pathify'

    import Input from '../components/Input.vue'
    import MaskedInput from '../components/MaskedInput.vue'
    import { api, constant } from '../utils'
    import { phone } from '../utils/validators'

    export default {
        name: 'EditUserPage',
        components: {
            Input,
            MaskedInput,
        },
        data () {
            return {
                confirmPassword: '',
                email: '',
                firstName: '',
                formError: '',
                lastName: '',
                password: '',
                phone: '',
            }
        },
        computed: {
            phoneMask: constant(['(##) ####-####', '(##) #####-####']),
            user: get('user/user'),
        },
        validations: {
            confirmPassword: { minChar: minLength(6), samePassword: sameAs('password') },
            email: { email, required },
            firstName: { required },
            lastName: { required },
            password: { minChar: minLength(6) },
            phone: { required, phone },
        },
        watch: {
            user () {
                this.email = this.user.user_email
                this.firstName = this.user.first_name
                this.lastName = this.user.last_name
                this.phone = this.user.phone
            }
        },
        methods: {
            async updateUser () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        const payload = {
                            email: this.email,
                            first_name: this.firstName,
                            last_name: this.lastName,
                            phone: this.phone,
                        }
                        if (this.password) {
                            payload.password = this.password
                        }
                        const user = await api.post('user/edit', payload)
                        if (this.password) {
                            await api.post('user/logout')
                            window.location.assign(`${window.IandeSettings.iandeUrl}/user/login`)
                        } else {
                            window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
                        }
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>