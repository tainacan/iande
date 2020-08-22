<template>
    <div class="iande-stack stack-lg">
        <h1>Pessoa Responsável</h1>
        <div>
            <label class="iande-label" for="isContact">Você é o contato responsável pela visita?</label>
            <RadioGroup id="isContact" v-model="isContact" :validations="$v.isContact" :options="binaryOptions"/>
        </div>
        <div v-show="!isContact">
            <div class="iande-label">Informe o contato da pessoa responsável</div>
            <div class="iande-form-grid">
                <Input id="firstName" type="text" placeholder="Nome" aria-label="Primeiro nome" v-model="firstName" :validations="$v.firstName"/>
                <Input id="lastName" type="text" placeholder="Sobrenome" aria-label="Sobrenome" v-model="lastName" :validations="$v.lastName"/>
                <Input id="email" type="email" placeholder="E-mail" aria-label="E-mail" v-model="email" :validations="$v.email"/>
                <MaskedInput id="phone" type="tel" :mask="phoneMask" placeholder="DDD + Telefone" aria-label="DDD + Telefone" v-model="phone" :validations="$v.phone"/>
            </div>
        </div>
        <div>
            <label class="iande-label" for="role">Informe sua relação com a instituição de ensino</label>
            <Select id="role" v-model="role" :validations="$v.role" :options="roleOptions" />
        </div>
    </div>
</template>

<script>
    import { email, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import Input from './Input.vue'
    import MaskedInput from './MaskedInput.vue'
    import RadioGroup from './RadioGroup.vue'
    import Select from './Select.vue'
    import { constant } from '../utils'
    import { phone } from '../utils/validators'

    export default {
        name: 'ResponsiblePerson',
        components: {
            Input,
            MaskedInput,
            RadioGroup,
            Select
        },
        data () {
            return {
                isContact: null,
            }
        },
        computed: {
            ...sync('appointments/current@', {
                firstName: 'responsible_first_name',
                email: 'responsible_email',
                lastName: 'responsible_last_name',
                phone: 'responsible_phone',
                role: 'responsible_role',
            }),
            binaryOptions: constant({ 'Sim': true, 'Não': false }),
            phoneMask: constant(['(##) ####-####', '(##) #####-####']),
            roleOptions: constant(window.IandeSettings.responsibleRoles),
            user: get('user/user')
        },
        validations: {
            firstName: { required },
            email: { email, required },
            isContact: { },
            lastName: { required },
            phone: { required, phone },
            role: { required },
        },
        created () {
            if (this.firstName && this.lastName && this.email && this.phone) {
                this.isContact = true
            } else {
                this.isContact = false
            }
        },
        watch: {
            isContact () {
                if (this.isContact) {
                    this.firstName = this.user.first_name
                    this.lastName = this.user.last_name
                    this.email = this.user.user_email
                    this.phone = this.user.phone
                } else {
                    this.firstName = ''
                    this.lastName = ''
                    this.email = ''
                    this.phone = ''
                }
            }
        }
    }
</script>