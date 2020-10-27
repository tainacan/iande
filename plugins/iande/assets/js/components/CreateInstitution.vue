<template>
    <div id="iande-create-institution" class="iande-stack stack-lg">
        <h1>Sobre a instituição</h1>
        <p v-if="!edit">Aqui você deve cadastrar dados referentes a instituição responsável pela visita. Cada instituição cadastrada ficará salva no seu perfil. Deste modo você poderá criar novas visitas com mais agilidade e menos campos para preencher.</p>
        <div>
            <label class="iande-label" for="name">Nome da instituição</label>
            <Input id="name" type="text" placeholder="Nome oficial da instituição" v-model="name" :validations="$v.name"/>
        </div>
        <div>
            <label class="iande-label" for="cnpj">CNPJ da instituição<span class="iande-label__optional">(opcional)</span></label>
            <MaskedInput id="cnpj" type="text" :mask="cnpjMask" placeholder="Digite o CNPJ da instituição" v-model="cnpj" :validations="$v.cnpj"/>
        </div>
        <div>
            <label class="iande-label" for="profile">Perfil da instituição</label>
            <Select id="profile" placeholder="Selecione o perfil da instituição" v-model="profile" :validations="$v.profile" :options="profileOptions"/>
        </div>
        <div v-if="isOther(profile)">
            <label class="iande-label" for="profileOther">Especifique o perfil da instituição</label>
            <Input id="profileOther" type="text" v-model="profileOther" :validations="$v.profileOther"/>
        </div>
        <div>
            <label for="phone" class="iande-label">Telefone</label>
            <MaskedInput id="phone" type="tel" :mask="phoneMask" placeholder="DDD + Telefone" v-model="phone" :validations="$v.phone"/>
        </div>
        <div>
            <label for="email" class="iande-label">E-mail</label>
            <Input id="email" type="email" placeholder="E-mail de contato da instituição" v-model="email" :validations="$v.email"/>
        </div>
        <div>
            <label for="zipCode" class="iande-label">CEP</label>
            <MaskedInput id="zipCode" type="text" :mask="cepMask" v-model="zipCode" :validations="$v.zipCode"/>
            <a class="iande-form-link" href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank">Descobrir CEP</a>
        </div>
        <div>
            <label for="address" class="iande-label">Endereço</label>
            <Input id="address" type="text" placeholder="Ex.: Avenida Brasil" v-model="address" :validations="$v.address"/>
        </div>
        <div class="iande-form-grid">
            <div>
                <label for="addressNumber" class="iande-label">Número</label>
                <Input id="addressNumber" type="text" v-model="addressNumber" :validations="$v.addressNumber"/>
            </div>
            <div>
                <label for="addressComplement" class="iande-label">Complemento</label>
                <Input id="addressComplement" type="text" placeholder="Ex.: Apto 12" v-model="addressComplement" :validations="$v.addressComplement"/>
            </div>
        </div>
        <div>
            <label for="district" class="iande-label">Bairro</label>
            <Input id="district" type="text" v-model="district" :validations="$v.district"/>
        </div>
        <div class="iande-form-grid one-two">
            <div>
                <label for="state" class="iande-label">UF</label>
                <Select id="state" v-model="state" :validations="$v.state" :options="stateOptions"/>
            </div>
            <div>
                <label for="city" class="iande-label">Cidade</label>
                <Select id="city" v-model="city" :validations="$v.city" :options="cityOptions"/>
            </div>
        </div>
    </div>
</template>

<script>
    import { email, required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import Input from './Input.vue'
    import MaskedInput from './MaskedInput.vue'
    import Select from './Select.vue'
    import { api, constant, isOther, sortBy, watchForOther } from '../utils'
    import { cep, cnpj, phone } from '../utils/validators'

    const cities = import(/* webpackChunkName: 'estados-municipios' */ '../../json/estados.json')
    const states = import(/* webpackChunkName: 'estados-municipios' */ '../../json/municipios.json')

    export default {
        name: 'CreateInstitution',
        props: {
            edit: { type: Boolean, default: false },
        },
        components: {
            Input,
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
            profileOptions: constant(window.IandeSettings.profiles),
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