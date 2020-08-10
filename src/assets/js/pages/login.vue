<template>
    <article>
        <div class="container stack-lg narrow">
            <h1>Boas vindas!</h1>
            <p class="slogan">Você está na plataforma de agendamento <span class="text-secondary">iandé</span> + {{ institution }}.</p>
            <p>Para agendar uma visita é simples. Basta você se logar e informar os dados solicitados nas 3 etapas a seguir:</p>
        </div>

        <StepsIndicator :step="0"/>

        <div class="container stack-lg narrow">
            <p>Após a confirmação, você receberá um email com o resumo sobre o agendamento. Vamos lá?</p>
            <form class="form stack-lg" @submit.prevent="login">
                <div>
                    <div class="label">Faça login para começar:</div>
                    <div class="grid">
                        <ValidatedInput id="email" type="text" placeholder="email" aria-label="E-mail" v-model="email" :validations="$v.email"/>
                        <div>
                            <ValidatedInput id="password" type="password" placeholder="senha" aria-label="Senha" v-model="password" :validations="$v.password"/>
                            <a class="link" href="#">Não lembro a senha</a>
                        </div>
                    </div>
                </div>
                <div class="stack-md">
                    <button class="button solid" type="submit">Fazer login</button>
                    <a class="button outline" href="../create">Criar login</a>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import StepsIndicator from '../components/StepsIndicator'
    import ValidatedInput from '../components/ValidatedInput'
    import api from '../utils/api'

    export default {
        name: 'LoginPage',
        components: {
            StepsIndicator,
            ValidatedInput,
        },
        props: {
            institution: { type: String, required: true },
        },
        data () {
            return {
                email: '',
                password: '',
            }
        },
        validations: {
            email: { required },
            password: { required },
        },
        methods: {
            async login () {
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        const user = await api.post('user/login', {
                            email: this.email,
                            password: this.password
                        })
                        this.$store.set('user/user', user)
                    } catch (err) {
                        console.error(err)
                    }
                }
            }
        }
    }
</script>