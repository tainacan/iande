<template>
    <section class="iande-institution">
        <div class="iande-institution__summary" :class="showDetails || 'collapsed'">
            <h2>{{ institution.name }}</h2>
            <div class="iande-institution__toggle" :aria-label="showDetails ? 'Ocultar detalhes' : 'Exibir detalhes'" role="button" tabindex="0" @click="toggleDetails" @keypress.enter="toggleDetails">
                <Icon :icon="showDetails ? 'minus-circle' : 'plus-circle'"/>
            </div>
        </div>
        <div class="iande-institution__details" v-if="showDetails">
            <div class="iande-institution__box">
                <div class="iande-institution__box-title">
                    <h3><Icon icon="university"/>Instituição</h3>
                    <div class="iande-institution__edit">
                        <a class="iande-institution__edit-link" :href="`${iandeUrl}/institution/edit?ID=${institution.ID}`">Editar</a>
                        <Icon icon="pencil-alt"/>
                    </div>
                </div>
                <div>
                    <div>{{ institutionProfile(institution) }}</div>
                    <div v-if="institution.cnpj">CNPJ: {{ formatCnpj(institution.cnpj) }}</div>
                    <div>Telefone: {{ formatPhone(institution.phone) }}</div>
                    <div>E-mail: {{ institution.email }}</div>
                </div>
                <div>
                    <div>
                        {{ institution.address }}, {{ institution.address_number }}
                        <template v-if="institution.complement">{{ institution.complement }}</template>
                        - {{ institution.district }}
                    </div>
                    <div>{{ city }} - {{ institution.state }}</div>
                    <div>CEP {{ formatCep(institution.zip_code) }}</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import { constant, formatCep, formatCnpj, formatPhone, isOther } from '../utils'

    // Lazy-loading candidates
    import municipios from '../../json/municipios.json'

    export default {
        name: 'InstitutionDetails',
        components: {
            Icon: FontAwesomeIcon,
        },
        props: {
            institution: { type: Object, required: true },
            n: { type: Number, default: 1 },
        },
        data () {
            return {
                showDetails: false,
            }
        },
        computed: {
            city () {
                const cityId = this.institution.city
                return Object.entries(municipios).find(([key]) => key === cityId)[1]
            },
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            name () {
                if (this.institution.name) {
                    return this.institution.name
                } else {
                    return `Instituição ${this.n}`
                }
            },
            siteName: constant(window.IandeSettings.siteName),
        },
        methods: {
            formatCep,
            formatCnpj,
            formatPhone,
            institutionProfile (institution) {
                if (isOther(institution.profile) && institution.profile_other) {
                    return institution.profile_other
                } else {
                    return institution.profile
                }
            },
            toggleDetails () {
                this.showDetails = !this.showDetails
            },
        }
    }
</script>