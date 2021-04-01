<template>
    <div class="iande-stack stack-lg">
        <h1>Natureza do grupo</h1>
        <p>Escolas, ONGs, fundações e outras instituições devem preencher o campo como <b>Grupo Institucional</b>. Famílias, grupos de amigos ou turistas devem se cadastrar como <b>Outros Grupos</b>.</p>
        <div>
            <label class="iande-label" for="isContact">Você é o contato responsável pela visita?</label>
            <RadioGroup id="isContact" v-model="isContact" :validations="$v.isContact" :options="binaryOptions"/>
        </div>
        <div v-show="isContact !== 'yes'">
            <div class="iande-label">Informe o contato da pessoa responsável</div>
            <div class="iande-form-grid">
                <Input id="firstName" type="text" placeholder="Nome" aria-label="Primeiro nome" v-model="firstName" :validations="$v.firstName"/>
                <Input id="lastName" type="text" placeholder="Sobrenome" aria-label="Sobrenome" v-model="lastName" :validations="$v.lastName"/>
                <Input id="email" type="email" placeholder="E-mail" aria-label="E-mail" v-model="email" :validations="$v.email"/>
                <MaskedInput id="phone" type="tel" :mask="phoneMask" placeholder="DDD + Telefone" aria-label="DDD + Telefone" v-model="phone" :validations="$v.phone"/>
            </div>
        </div>
        <div v-if="requireExemption">
            <label class="iande-label" for="requestedExemption">Deseja solicitar formulário de isenção de ingresso?</label>
            <RadioGroup id="requestedExemption" v-model="requestedExemption" :validations="$v.requestedExemption" :options="binaryOptions"/>
        </div>
        <div>
            <label class="iande-label" for="nature">Natureza do grupo</label>
            <Select id="nature" v-model="nature" :validations="$v.nature" :options="natureOptions"/>
        </div>
        <template v-if="!institutionOptional">
            <div>
                <label class="iande-label" for="role">Informe sua relação com a instituição</label>
                <Select id="role" v-model="role" :validations="$v.role" :options="$iande.responsibleRoles" />
            </div>
            <div v-if="isOther(role)">
                <label class="iande-label" for="roleOther">Especifique sua relação com a instituição</label>
                <Input id="roleOther" type="text" v-model="roleOther" :validations="$v.roleOther"/>
            </div>
            <div>
                <label class="iande-label" for="institution">Instituição responsável pela visita</label>
                <Select id="institution" v-model="institution" :validations="$v.institution" empty="Você ainda não possui instituições cadastradas ⚠️" :options="institutionOptions"/>
            </div>
            <div class="iande-add-item" v-if="canAddInstitution" role="button" tabindex="0" @click="addInstitution">
                <span><Icon icon="plus-circle"/></span>
                <div class="iande-label">Adicionar uma instituição</div>
            </div>
        </template>
    </div>
</template>

<script>
    import { email, required, requiredUnless } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import Input from './Input.vue'
    import MaskedInput from './MaskedInput.vue'
    import RadioGroup from './RadioGroup.vue'
    import Select from './Select.vue'
    import { api, constant, isOther, watchForOther } from '../utils'
    import { phone } from '../utils/validators'

    const requireExemption = window.IandeSettings.useExemption === 'yes'

    export default {
        name: 'SelectInstitution',
        components: {
            Input,
            MaskedInput,
            RadioGroup,
            Select,
        },
        props: {
            canAddInstitution: { type: Boolean, default: true }
        },
        data () {
            return {
                isContact: null,
                skipInstitution: false,
            }
        },
        computed: {
            ...sync('appointments/current@', {
                firstName: 'responsible_first_name',
                email: 'responsible_email',
                institution: 'institution_id',
                lastName: 'responsible_last_name',
                nature: 'group_nature',
                phone: 'responsible_phone',
                requestedExemption: 'requested_exemption',
                role: 'responsible_role',
                roleOther: 'responsible_role_other',
            }),
            binaryOptions: constant({ 'Sim': 'yes', 'Não': 'no' }),
            institutionOptional () {
                return (this.nature && this.nature === 'other') || this.skipInstitution
            },
            institutionOptions () {
                const entries = this.institutions.map(({ ID, name }) => [name || `#${ID}`, String(ID)])
                return Object.fromEntries(entries)
            },
            institutions: sync('institutions/list'),
            natureOptions: constant({
                'Grupo Institucional': 'institutional',
                'Outra': 'other'
            }),
            phoneMask: constant(['(##) ####-####', '(##) #####-####']),
            requireExemption: constant(requireExemption),
            user: get('user/user'),
        },
        validations: {
            firstName: { required },
            email: { email, required },
            institution: { required: requiredUnless('institutionOptional') },
            isContact: { },
            lastName: { required },
            nature: { required },
            phone: { required, phone },
            requestedExemption: requireExemption ? { required } : { },
            role: { required: requiredUnless('institutionOptional') },
            roleOther: { },
        },
        async created () {
            if (this.firstName && this.lastName && this.email && this.phone) {
                this.isContact = 'yes'
            } else {
                this.isContact = 'no'
            }

            const institutions = await api.get('institution/list')
            this.institutions = institutions
        },
        watch: {
            role: watchForOther('role', 'roleOther'),
            isContact () {
                if (this.isContact === 'yes') {
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
        },
        methods: {
            addInstitution () {
                this.skipInstitution = true
                this.$nextTick(() => {
                    this.$emit('add-institution')
                })
            },
            isOther,
        }
    }
</script>