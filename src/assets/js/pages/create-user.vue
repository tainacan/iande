<template>
    <article>
        <div class="container stack-lg narrow">
            <h1>Boas vindas!</h1>
            <p class="slogan">Você está na plataforma de agendamento <span class="text-secondary">iandé</span> + {{ institution }}.</p>
            <p>Para agendar uma visita é simples. Basta você se logar e informar os dados solicitados nas 3 etapas a seguir:</p>
        </div>

        <StepsIndicator :step="0"/>

        <div class="container stack-lg narrow">
            <p>Ao fim da etapa 3 você, receberá um email com resumo e informações importantes do agendamento. Vamos lá?</p>
            <form class="form stack-lg" @submit.prevent="createUser">
                <div>
                    <div class="label">Faça login para começar:</div>
                    <div class="grid">
                        <ValidatedInput id="firstName" type="text" placeholder="nome" aria-label="Primeiro nome" v-model="firstName" :validations="$v.firstName"/>
                        <ValidatedInput id="lastName" type="text" placeholder="sobrenome" aria-label="Sobrenome" v-model="lastName" :validations="$v.lastName"/>
                        <ValidatedInput id="email" type="email" placeholder="email" aria-label="E-mail" v-model="email" :validations="$v.email"/>
                        <ValidatedMaskedInput id="phone" type="tel" :mask="phoneMask" placeholder="DDD + telefone" aria-label="DDD + Telefone" v-model="phone" :validations="$v.phone"/>
                        <ValidatedInput id="password" type="password" placeholder="senha" aria-label="Senha" v-model="password" :validations="$v.password"/>
                        <ValidatedInput id="confirmPassword" type="password" placeholder="confirmar senha" aria-label="Confirmar senha" v-model="confirmPassword" :validations="$v.confirmPassword"/>
                    </div>
                </div>
                <div class="stack-md">
                    <div class="form-error" v-if="formError">
                        <span>{{ formError }}</span>
                    </div>
                    <button class="button solid" type="submit">Criar login</button>
                    <a class="button outline" href="../login">Já tenho login</a>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { email, minLength, required, sameAs } from 'vuelidate/lib/validators'

    import StepsIndicator from '../components/StepsIndicator.vue'
    import ValidatedMaskedInput from '../components/ValidatedMaskedInput.vue'
    import ValidatedInput from '../components/ValidatedInput.vue'
    import api from '../utils/api'
    import { phone } from '../utils/validators'

    export default {
        name: 'CreateUserPage',
        components: {
            StepsIndicator,
            ValidatedMaskedInput,
            ValidatedInput,
        },
        props: {
            institution: { type: String, required: true },
        },
        data () {
            return {
                confirmPassword: '',
                firstName: '',
                email: '',
                formError: '',
                lastName: '',
                password: '',
                phone: '',
            }
        },
        computed: {
            phoneMask() {
                return ['(##) ####-####', '(##) #####-####']
            }
        },
        validations: {
            confirmPassword: { required, minLength: minLength(6), samePassword: sameAs('password') },
            firstName: { required },
            email: { email, required },
            lastName: { required },
            password: { required, minLength: minLength(6) },
            phone: { required, phone },
        },
        methods: {
            async createUser () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.invalid) {
                    try {
                        const user = await api.post('user/create', {
                            email: this.email,
                            first_name: this.firstName,
                            last_name: this.lastName,
                            password: this.password,
                            phone: this.phone
                        })
                        this.$store.set('user/user', user)
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>