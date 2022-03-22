<template>
    <article id="iande-login" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>{{ __('Boas vindas!', 'iande') }}</h1>
            <p class="slogan">{{ __('Você está na plataforma de visitação do', 'iande') }} <span class="text-secondary">iandé</span>&nbsp;+&nbsp;{{ $iande.siteName }}.</p>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="createUser">
                <div>
                    <div class="iande-label">{{ __('Crie uma conta para começar:', 'iande') }}</div>
                    <div class="iande-form-grid">
                        <Input id="firstName" type="text" autocomplete="given-name" :placeholder="__('Nome', 'iande')" :aria-label="__('Primeiro nome', 'iande')" v-model="firstName" :v="$v.firstName"/>
                        <Input id="lastName" type="text" autocomplete="family-name" :placeholder="__('Sobrenome', 'iande')" :aria-label="__('Sobrenome', 'iande')" v-model="lastName" :v="$v.lastName"/>
                        <Input id="email" type="email" autocomplete="email" :placeholder="__('E-mail', 'iande')" :aria-label="__('E-mail', 'iande')" v-model="email" :v="$v.email"/>
                        <MaskedInput id="phone" type="tel" autocomplete="tel-national" :mask="phoneMask" :placeholder="__('DDD + Telefone', 'iande')" :aria-label="__('DDD + Telefone', 'iande')" v-model="phone" :v="$v.phone"/>
                        <Input id="password" type="password" autocomplete="new-password" :placeholder="__('Senha', 'iande')" :aria-label="__('Senha', 'iande')" v-model="password" :v="$v.password"/>
                        <Input id="confirmPassword" type="password" autocomplete="new-password" :placeholder="__('Confirmar senha', 'iande')" :aria-label="__('Confirmar senha', 'iande')" v-model="confirmPassword" :v="$v.confirmPassword"/>
                    </div>
                </div>
                <div class="iande-stack stack-md">
                    <div class="iande-form-error" v-if="formError">
                        <span>{{ __(formError, 'iande') }}</span>
                    </div>
                    <button class="iande-button primary" type="submit">{{ __('Criar login', 'iande') }}</button>
                    <a class="iande-button outline" :href="$iandeUrl('user/login')">{{ __('Já tenho login', 'iande') }}</a>
                    <p class="iande-privacy-policy" v-if="$iande.privacyPolicy">
                        <a :href="$iande.privacyPolicy">{{ __('Política de Privacidade', 'iande') }}</a>
                    </p>
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
