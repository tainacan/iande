<template>
    <section class="iande-institution">
        <div class="iande-institution__summary" :class="showDetails || 'collapsed'">
            <h2>{{ institution.name }}</h2>
            <div class="iande-institution__toggle" :aria-label="showDetails ? __('Ocultar detalhes', 'iande') : __('Exibir detalhes', 'iande')" role="button" tabindex="0" @click="toggleDetails" @keypress.enter="toggleDetails">
                <Icon :icon="showDetails ? 'circle-minus' : 'circle-plus'"/>
            </div>
        </div>
        <div class="iande-institution__details" v-if="showDetails">
            <div class="iande-institution__box">
                <div class="iande-institution__box-title">
                    <h3><Icon icon="landmark"/>{{ __('Instituição', 'iande') }}</h3>
                    <div class="iande-institution__edit">
                        <a class="iande-institution__edit-link" :href="$iandeUrl(`institution/edit?ID=${institution.ID}`)">{{ __('Editar', 'iande') }}</a>
                        <Icon icon="pen"/>
                    </div>
                </div>
                <div>
                    <div>{{ institutionProfile(institution) }}</div>
                    <div v-if="institution.cnpj">{{ __('CNPJ', 'iande') }}: {{ formatCnpj(institution.cnpj) }}</div>
                    <div>{{ __('Telefone', 'iande') }}: {{ formatPhone(institution.phone) }}</div>
                    <div>{{ __('E-mail', 'iande') }}: {{ institution.email }}</div>
                </div>
                <div>
                    <div>
                        {{ institution.address }}, {{ institution.address_number }}
                        <template v-if="institution.complement">{{ institution.complement }}</template>
                        - {{ institution.district }}
                    </div>
                    <div>{{ city }} - {{ institution.state }}</div>
                    <div>{{ __('CEP', 'iande') }} {{ formatCep(institution.zip_code) }}</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import { __, sprintf } from '@plugins/wp-i18n'
    import { formatCep, formatCnpj, formatPhone, isOther } from '@utils'

    const cities = import(/* webpackChunkName: 'estados-municipios' */ '../../json/municipios.json')

    export default {
        name: 'InstitutionDetails',
        props: {
            institution: { type: Object, required: true },
            n: { type: Number, default: 1 },
        },
        data () {
            return {
                showDetails: false,
            }
        },
        asyncComputed: {
            cities: {
                get () {
                    return cities
                },
                default: {},
            },
        },
        computed: {
            city () {
                const cityId = this.institution.city
                return Object.entries(this.cities).find(([key]) => key === cityId)[1]
            },
            name () {
                if (this.institution.name) {
                    return this.institution.name
                } else {
                    return sprintf(__('Instituição %s', 'iande'), this.n)
                }
            },
        },
        methods: {
            formatCep,
            formatCnpj,
            formatPhone,
            institutionProfile (institution) {
                if (isOther(institution.profile) && institution.profile_other) {
                    return __(institution.profile_other, 'iande')
                } else {
                    return __(institution.profile, 'iande')
                }
            },
            toggleDetails () {
                this.showDetails = !this.showDetails
            },
        }
    }
</script>
