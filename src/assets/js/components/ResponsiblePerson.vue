<template>
    <div class="iande-stack stack-lg">
        <h1>Pessoa Responsável</h1>
        <div>
            <label class="iande-label" for="is-contact">Você é o contato responsável pela visita?</label>
            <RadioGroup id="is-contact" v-model="isContact" :validations="$v.isContact" :options="binaryOptions"/>
        </div>
        <div>
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
            <Select id="role" v-model="role" :validations="$v.role" :options="[]" />
        </div>
    </div>
</template>

<script>
    import { email, required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import Input from '../components/Input.vue'
    import MaskedInput from '../components/MaskedInput.vue'
    import RadioGroup from '../components/RadioGroup.vue'
    import Select from '../components/Select.vue'
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
                role: '',
            }
        },
        computed: {
            ...sync('appointments/current@', {
                firstName: 'responsible_first_name',
                email: 'responsible_email',
                lastName: 'responsible_last_name',
                phone: 'responsible_phone',
            }),
            binaryOptions: constant([[true, 'Sim'], [false, 'Não']]),
            phoneMask: constant(['(##) ####-####', '(##) #####-####'])
        },
        validations: {
            firstName: { required },
            email: { email, required },
            isContact: { },
            lastName: { required },
            phone: { required, phone },
            role: { required },
        }

    }
</script>