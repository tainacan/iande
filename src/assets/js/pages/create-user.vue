<template>
    <article id="iande-login" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Boas vindas!</h1>
            <p class="slogan">Você está na plataforma de visitação do <span class="text-secondary">iandé</span>&nbsp;+&nbsp;{{ $iande.siteName }}.</p>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="createUser">
                <div>
                    <div class="iande-label">Faça login para começar:</div>
                    <div class="iande-form-grid">
                        <Input id="firstName" type="text" autocomplete="given-name" placeholder="Nome" aria-label="Primeiro nome" v-model="firstName" :validations="$v.firstName"/>
                        <Input id="lastName" type="text" autocomplete="family-name" placeholder="Sobrenome" aria-label="Sobrenome" v-model="lastName" :validations="$v.lastName"/>
                        <Input id="email" type="email" autocomplete="email" placeholder="E-mail" aria-label="E-mail" v-model="email" :validations="$v.email"/>
                        <MaskedInput id="phone" type="tel" autocomplete="tel-national" :mask="phoneMask" placeholder="DDD + Telefone" aria-label="DDD + Telefone" v-model="phone" :validations="$v.phone"/>
                        <Input id="password" type="password" autocomplete="new-password" placeholder="Senha" aria-label="Senha" v-model="password" :validations="$v.password"/>
                        <Input id="confirmPassword" type="password" autocomplete="new-password" placeholder="Confirmar senha" aria-label="Confirmar senha" v-model="confirmPassword" :validations="$v.confirmPassword"/>
                    </div>
                </div>
                <div class="iande-stack stack-md">
                    <div class="iande-form-error" v-if="formError">
                        <span>{{ formError }}</span>
                    </div>
                    <button class="iande-button primary" type="submit">Criar login</button>
                    <a class="iande-button outline" :href="$iandeUrl('user/login')">Já tenho login</a>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { email, minLength, required, sameAs } from 'vuelidate/lib/validators'

    import Input from '@components/Input.vue'
    import MaskedInput from '@components/MaskedInput.vue'
    import { api, constant } from '@utils'
    import { phone } from '@utils/validators'

    export default {
        name: 'CreateUserPage',
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
        },
        validations: {
            confirmPassword: { required, minChar: minLength(6), samePassword: sameAs('password') },
            email: { email, required },
            firstName: { required },
            lastName: { required },
            password: { required, minChar: minLength(6) },
            phone: { required, phone },
        },
        methods: {
            async createUser () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        let recaptchaToken = undefined
                        if (this.$iande.recaptchaKey) {
                            await this.$recaptchaLoaded()
                            recaptchaToken = await this.$recaptcha('login')
                        }
                        const user = await api.post('user/create', {
                            email: this.email,
                            first_name: this.firstName,
                            last_name: this.lastName,
                            password: this.password,
                            phone: this.phone,
                            recaptcha: recaptchaToken,
                        })
                        await this.$store.set('users/current', user)
                        window.location.assign(this.$iandeUrl('user/welcome'))
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>
