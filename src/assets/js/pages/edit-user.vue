<template>
    <article id="iande-edit-user" class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>{{ __('Edição de usuário', 'iande') }}</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="updateUser">
                <div>
                    <label class="iande-label" for="firstName">{{ __('Nome', 'iande') }}</label>
                    <Input id="firstName" type="text" v-model="firstName" :validations="$v.firstName"/>
                </div>
                <div>
                    <label class="iande-label" for="lastName">{{ __('Sobrenome', 'iande') }}</label>
                    <Input id="lastName" type="text" v-model="lastName" :validations="$v.lastName"/>
                </div>
                <div>
                    <label class="iande-label" for="email">{{ __('E-mail', 'iande') }}</label>
                    <Input id="email" type="email" v-model="email" :validations="$v.email"/>
                </div>
                <div>
                    <label class="iande-label" for="phone">{{ __('Telefone', 'iande') }}</label>
                    <MaskedInput id="phone" type="tel" :mask="phoneMask" :placeholder="__('DDD + Telefone', 'iande')" v-model="phone" :validations="$v.phone"/>
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
    import { email, required } from 'vuelidate/lib/validators'
    import { get } from 'vuex-pathify'

    import Input from '@components/Input.vue'
    import MaskedInput from '@components/MaskedInput.vue'
    import { api, constant } from '@utils'
    import { phone } from '@utils/validators'

    export default {
        name: 'EditUserPage',
        components: {
            Input,
            MaskedInput,
        },
        data () {
            return {
                email: '',
                firstName: '',
                formError: '',
                lastName: '',
                phone: '',
            }
        },
        computed: {
            phoneMask: constant(['(##) ####-####', '(##) #####-####']),
            user: get('users/current'),
        },
        validations: {
            email: { email, required },
            firstName: { required },
            lastName: { required },
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
                        const user = await api.post('user/edit', payload)
                        window.location.assign(this.$iandeUrl('user/welcome'))
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>