<template>
    <div class="iande-stack stack-lg">
        <h1>Natureza do grupo</h1>
        <p>Escolas, ONGs, fundações e outras instituições devem preencher o campo como <b>Grupo Institucional</b>. Famílias, grupos de amigos ou turistas devem se cadastrar como <b>Outros Grupos</b>.</p>
        <div>
            <label class="iande-label" for="nature">Natureza do grupo</label>
            <Select id="nature" v-model="nature" :validations="$v.nature" :options="natureOptions"/>
        </div>
        <template v-if="nature">
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
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { required, requiredUnless } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import Select from '../components/Select.vue'
    import { api, constant } from '../utils'

    export default {
        name: 'SelectInstitution',
        components: {
            Icon: FontAwesomeIcon,
            Select
        },
        props: {
            canAddInstitution: { type: Boolean, default: true }
        },
        data () {
            return {
                skipInstitution: false,
            }
        },
        computed: {
            ...sync('appointments/current@', {
                institution: 'institution',
                nature: 'group_nature',
            }),
            institutionOptional () {
                return (this.nature && this.nature === 'outro') || this.skipInstitution
            },
            institutionOptions () {
                const entries = this.institutions.map(({ ID, name }) => [name || `#${ID}`, String(ID)])
                return Object.fromEntries(entries)
            },
            institutions: sync('institutions/list'),
            natureOptions: constant({
                'Grupo Institucional': 'institutional',
                'Outra': 'other'
            })
        },
        validations: {
            institution: { required: requiredUnless('institutionOptional') },
            nature: { required },
        },
        async created () {
            if (this.institutions.length === 0) {
                const institutions = await api.get('institution/list')
                this.institutions = institutions
            }
        },
        methods: {
            addInstitution () {
                this.skipInstitution = true
                this.$nextTick(() => {
                    this.$emit('add-institution')
                })
            }
        }
    }
</script>