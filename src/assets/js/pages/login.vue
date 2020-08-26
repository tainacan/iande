<template>
    <article id="iande-login" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Boas vindas!</h1>
            <p class="slogan">Você está na plataforma de agendamento <span class="text-secondary">iandé</span> + {{ siteName }}.</p>
            <p>Para agendar uma visita é simples. Basta você se logar e informar os dados solicitados nas 3 etapas a seguir:</p>
        </div>

        <StepsIndicator :step="0"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <p>Após a confirmação, você receberá um email com o resumo sobre o agendamento. Vamos lá?</p>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="login">
                <div>
                    <div class="iande-label">Faça login para começar:</div>
                    <div class="iande-form-grid">
                        <Input id="email" type="text" placeholder="E-mail" aria-label="E-mail" v-model="email" :validations="$v.email"/>
                        <div>
                            <Input id="password" type="password" placeholder="Senha" aria-label="Senha" v-model="password" :validations="$v.password"/>
                            <a class="iande-form-link" :href="resetPassword">Não lembro a senha</a>
                        </div>
                    </div>
                </div>
                <div class="iande-stack stack-md">
                    <div class="iande-form-error" v-if="formError">
                        <span>{{ formError }}</span>
                    </div>
                    <button class="iande-button primary" type="submit">Fazer login</button>
                    <a class="iande-button outline" :href="`${iandeUrl}/user/create`">Criar login</a>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import Input from '../components/Input.vue'
    import StepsIndicator from '../components/StepsIndicator.vue'
    import{ api, constant } from '../utils'

    export default {
        name: 'LoginPage',
        components: {
            Input,
            StepsIndicator,
        },
        props: {
            resetPassword: { type: String, default: '#' }
        },
        data () {
            return {
                email: '',
                formError: '',
                password: '',
            }
        },
        computed: {
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            siteName: constant(window.IandeSettings.siteName),
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
                        const user = await api.post('user/login', {
                            email: this.email,
                            password: this.password
                        })
                        await this.$store.set('user/user', user)
                        window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>