<template>
    <div id="iande-create-institution" class="iande-stack stack-lg">
        <h1>{{ __('Sobre a instituição', 'iande') }}</h1>
        <p v-if="!edit">{{ __('Aqui você deve cadastrar dados referentes a instituição responsável pela visita. Cada instituição cadastrada ficará salva no seu perfil. Deste modo você poderá criar novas visitas com mais agilidade e menos campos para preencher.', 'iande') }}</p>
        <div>
            <Label for="name">{{ __('Nome da instituição', 'iande') }}</Label>
            <Input id="name" type="text" :placeholder="__('Nome oficial da instituição', 'iande')" v-model="name" :v="$v.name"/>
        </div>
        <div>
            <Label for="cnpj" :side="__('(opcional)', 'iande')">{{ __('CNPJ da instituição', 'iande') }}</Label>
            <MaskedInput id="cnpj" type="text" :mask="cnpjMask" :placeholder="__('Digite o CNPJ da instituição', 'iande')" v-model="cnpj" :v="$v.cnpj"/>
        </div>
        <div>
            <Label for="profile">{{ __('Perfil da instituição', 'iande') }}</Label>
            <Select id="profile" :placeholder="__('Selecione o perfil da instituição', 'iande')" v-model="profile" :v="$v.profile" :options="$iande.profiles"/>
        </div>
        <div v-if="isOther(profile)">
            <Label for="profileOther">{{ __('Especifique o perfil da instituição', 'iande') }}</Label>
            <Input id="profileOther" type="text" v-model="profileOther" :v="$v.profileOther"/>
        </div>
        <div>
            <Label for="phone">{{ __('Telefone', 'iande') }}</Label>
            <MaskedInput id="phone" type="tel" :mask="phoneMask" :placeholder="__('DDD + Telefone', 'iande')" v-model="phone" :v="$v.phone"/>
        </div>
        <div>
            <Label for="email">{{ __('E-mail', 'iande') }}</Label>
            <Input id="email" type="email" :placeholder="__('E-mail de contato da instituição', 'iande')" v-model="email" :v="$v.email"/>
        </div>
        <div>
            <Label for="zipCode">{{ __('CEP', 'iande') }}</Label>
            <MaskedInput id="zipCode" type="text" :mask="cepMask" v-model="zipCode" :v="$v.zipCode"/>
            <a class="iande-form-link" href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank">{{ __('Descobrir CEP', 'iande') }}</a>
        </div>
        <div>
            <Label for="address">{{ __('Endereço', 'iande') }}</Label>
            <Input id="address" type="text" :placeholder="__('Ex.: Avenida Brasil', 'iande')" v-model="address" :v="$v.address"/>
        </div>
        <div class="iande-form-grid">
            <div>
                <Label for="addressNumber">{{ __('Número', 'iande') }}</Label>
                <Input id="addressNumber" type="text" v-model="addressNumber" :v="$v.addressNumber"/>
            </div>
            <div>
                <Label for="addressComplement">{{ __('Complemento', 'iande') }}</Label>
                <Input id="addressComplement" type="text" :placeholder="__('Ex.: Apto 12', 'iande')" v-model="addressComplement" :v="$v.addressComplement"/>
            </div>
        </div>
        <div>
            <Label for="district">{{ __('Bairro', 'iande') }}</Label>
            <Input id="district" type="text" v-model="district" :v="$v.district"/>
        </div>
        <div class="iande-form-grid one-two">
            <div>
                <Label for="state">{{ __('UF', 'iande') }}</Label>
                <Select id="state" v-model="state" :v="$v.state" :options="stateOptions"/>
            </div>
            <div>
                <Label for="city">{{ __('Cidade', 'iande') }}</Label>
                <Select id="city" v-model="city" :v="$v.city" :options="cityOptions"/>
            </div>
        </div>
    </div>
</template>

<script>
    import { email, required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import MaskedInput from '@components/MaskedInput.vue'
    import Select from '@components/Select.vue'
    import { api, constant, isOther, sortBy, watchForOther } from '@utils'
    import { cep, cnpj, phone } from '@utils/validators'

    const cities = import(/* webpackChunkName: 'estados-municipios' */ '../../json/municipios.json')
    const states = import(/* webpackChunkName: 'estados-municipios' */ '../../json/estados.json')

    export default {
        name: 'CreateInstitution',
        props: {
            edit: { type: Boolean, default: false },
        },
        components: {
            Input,
            Label,
            MaskedInput,
            Select
        },
        asyncComputed: {
            cities: {
                get () {
                    return cities
                },
                default: {},
            },
            states: {
                get () {
                    return states
                },
                default: {},
            }
        },
        computed: {
            ...sync('institutions/current@', {
                address: 'address',
                addressComplement: 'complement',
                addressNumber: 'address_number',
                city: 'city',
                cnpj: 'cnpj',
                district: 'district',
                email: 'email',
                name: 'name',
                phone: 'phone',
                profile: 'profile',
                profileOther: 'profile_other',
                state: 'state',
                zipCode: 'zip_code'
            }),
            cepMask: constant('#####-###'),
            cityOptions () {
                if (!this.state) {
                    return []
                }
                const entries = Object.entries(this.cities)
                    .filter(([sigla]) => sigla.startsWith(this.state))
                    .map(([sigla, nome]) => [nome, sigla])
                    .sort(sortBy(city => city[1]))
                return Object.fromEntries(entries)
            },
            cnpjMask: constant('##.###.###/####-##'),
            phoneMask: constant(['(##) ####-####', '(##) #####-####']),
            stateOptions () {
                const entries = Object.keys(this.states)
                    .map(state => [state, state])
                    .sort(sortBy(state => state[0]))
                return Object.fromEntries(entries)
            },
        },
        validations: {
            address: { required },
            addressComplement: { },
            addressNumber: { required },
            city: { required },
            cnpj: { cnpj },
            district: { required },
            email: { required, email },
            name: { required },
            phone: { required, phone },
            profile: { required },
            profileOther: { },
            state: { required },
            zipCode: { required, cep }
        },
        watch: {
            profile: watchForOther('profile', 'profileOther'),
            state () {
                if (!this.city.startsWith(this.state)) {
                    this.city = ''
                }
            },
            async zipCode () {
                if (this.zipCode && cep(this.zipCode)) {
                    try {
                        const res = await api.get(`https://viacep.com.br/ws/${this.zipCode}/json/`)
                        if (!res.erro) {
                            this.address = res.logradouro || ''
                            this.addressComplement = res.complemento || ''
                            this.addressNumber = ''
                            this.city = `${res.uf}${res.ibge.slice(2)}`
                            this.district = res.bairro || ''
                            this.state = res.uf
                        }
                    } catch (err) {
                        console.error(err)
                    }
                }
            }
        },
        methods: {
            isOther,
        }
    }
</script>
