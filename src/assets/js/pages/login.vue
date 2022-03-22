<template>
    <article id="iande-login" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>{{ __('Boas vindas!', 'iande') }}</h1>
            <p class="slogan">{{ __('Você está na plataforma de visitação do', 'iande') }} <span class="text-secondary">iandé</span>&nbsp;+&nbsp;{{ $iande.siteName }}.</p>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="login">
                <div>
                    <div class="iande-label">{{ __('Faça login para começar:', 'iande') }}</div>
                    <div class="iande-form-grid">
                        <Input id="email" type="text" autocomplete="email" :placeholder="__('E-mail', 'iande')" :aria-label="__('E-mail', 'iande')" v-model="email" :v="$v.email"/>
                        <div>
                            <Input id="password" type="password" autocomplete="current-password" :placeholder="__('Senha', 'iande')" :aria-label="__('Senha', 'iande')" v-model="password" :v="$v.password"/>
                            <a class="iande-form-link" :href="resetPassword">{{ __('Não lembro a senha', 'iande') }}</a>
                        </div>
                    </div>
                </div>
                <div class="iande-stack stack-md">
                    <div class="iande-form-error" v-if="formError">
                        <span>{{ __(formError, 'iande') }}</span>
                    </div>
                    <button class="iande-button primary" type="submit">{{ __('Fazer login', 'iande') }}</button>
                    <a class="iande-button outline" :href="$iandeUrl('user/create')">{{ __('Criar login', 'iande') }}</a>
                    <p class="iande-privacy-policy" v-if="$iande.privacyPolicy">
                        <a :href="$iande.privacyPolicy">{{ __('Política de Privacidade', 'iande') }}</a>
                    </p>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import Input from '@components/Input.vue'
    import { api } from '@utils'

    export default {
        name: 'LoginPage',
        components: {
            Input,
        },
        props: {
            next: { type: String, default: '/iande/user/welcome' },
            resetPassword: { type: String, default: '#' }
        },
        data () {
            return {
                email: '',
                formError: '',
                password: '',
            }
        },
        validations: {
            email: { required },
            password: { required },
        },
        methods: {
            async login () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        let recaptchaToken = undefined
                        if (this.$iande.recaptchaKey) {
                            await this.$recaptchaLoaded()
                            recaptchaToken = await this.$recaptcha('login')
                        }
                        const user = await api.post('user/login', {
                            email: this.email,
                            password: this.password,
                            recaptcha: recaptchaToken,
                        })
                        await this.$store.set('users/current', user)
                        window.location.assign(`${this.$iande.siteUrl}${this.next}`)
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>
