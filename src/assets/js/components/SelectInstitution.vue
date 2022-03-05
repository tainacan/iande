<template>
    <div class="iande-stack stack-lg">
        <h1>{{ __('Natureza do grupo', 'iande') }}</h1>
        <p v-html="__('Escolas, ONGs, fundações e outras instituições devem preencher o campo como <b>Grupo Institucional</b>. Famílias, grupos de amigos ou turistas devem se cadastrar como <b>Outra</b>.', 'iande')"/>
        <div>
            <Label for="isContact">{{ __('Você é o contato responsável pela visita?', 'iande') }}</Label>
            <RadioGroup id="isContact" v-model="isContact" :v="$v.isContact" :options="binaryOptions"/>
        </div>
        <div v-show="isContact !== 'yes'">
            <div class="iande-label">{{ __('Informe o contato da pessoa responsável', 'iande') }}</div>
            <div class="iande-form-grid">
                <Input id="firstName" type="text" :placeholder="__('Nome', 'iande')" :aria-label="__('Primeiro nome', 'iande')" v-model="firstName" :v="$v.firstName"/>
                <Input id="lastName" type="text" :placeholder="__('Sobrenome', 'iande')" :aria-label="__('Sobrenome', 'iande')" v-model="lastName" :v="$v.lastName"/>
                <Input id="email" type="email" :placeholder="__('E-mail', 'iande')" :aria-label="__('E-mail', 'iande')" v-model="email" :v="$v.email"/>
                <MaskedInput id="phone" type="tel" :mask="phoneMask" :placeholder="__('DDD + Telefone', 'iande')" :aria-label="__('DDD + Telefone', 'iande')" v-model="phone" :v="$v.phone"/>
            </div>
        </div>
        <div v-if="requireExemption">
            <Label for="requestedExemption">{{ __('Deseja solicitar formulário de isenção de ingresso?', 'iande') }}</Label>
            <RadioGroup id="requestedExemption" v-model="requestedExemption" :v="$v.requestedExemption" :options="binaryOptions"/>
        </div>
        <div>
            <Label for="nature">{{ __('Natureza do grupo', 'iande') }}</Label>
            <Select id="nature" v-model="nature" :v="$v.nature" :options="natureOptions"/>
        </div>
        <template v-if="!institutionOptional">
            <div>
                <Label for="role">{{ __('Informe sua relação com a instituição', 'iande') }}</Label>
                <Select id="role" v-model="role" :v="$v.role" :options="$iande.responsibleRoles" />
            </div>
            <div v-if="isOther(role)">
                <Label for="roleOther">{{ __('Especifique sua relação com a instituição', 'iande') }}</Label>
                <Input id="roleOther" type="text" v-model="roleOther" :v="$v.roleOther"/>
            </div>
            <div>
                <Label for="institution">{{ __('Instituição responsável pela visita', 'iande') }}</Label>
                <Select id="institution" v-model="institution" :v="$v.institution" :options="institutionOptions"/>
            </div>
            <div class="iande-add-item" v-if="canAddInstitution" role="button" tabindex="0" @click="addInstitution">
                <span><Icon icon="circle-plus"/></span>
                <div class="iande-label">{{ __('Adicionar uma instituição', 'iande') }}</div>
            </div>
        </template>
    </div>
</template>

<script>
    import { email, required, requiredUnless } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import MaskedInput from '@components/MaskedInput.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import Select from '@components/Select.vue'
    import { __, _x } from '@plugins/wp-i18n'
    import { api, constant, isOther, watchForOther } from '@utils'
    import { phone } from '@utils/validators'

    const requireExemption = window.IandeSettings.useExemption === 'yes'

    export default {
        name: 'SelectInstitution',
        components: {
            Input,
            Label,
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
            binaryOptions: constant({
                [__('Sim', 'iande')]: 'yes',
                [__('Não', 'iande')]: 'no',
            }),
            institutionOptional () {
                return (this.nature && this.nature === 'other') || this.skipInstitution
            },
            institutionOptions () {
                const entries = this.institutions.map(({ ID, name }) => [name || `#${ID}`, String(ID)])
                return Object.fromEntries(entries)
            },
            institutions: sync('institutions/list'),
            natureOptions: constant({
                [__('Grupo Institucional', 'iande')]: 'institutional',
                [_x('Outra', 'group', 'iande')]: 'other'
            }),
            phoneMask: constant(['(##) ####-####', '(##) #####-####']),
            requireExemption: constant(requireExemption),
            user: get('users/current'),
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

            const institutions = await api.get('institution/list_published')
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
