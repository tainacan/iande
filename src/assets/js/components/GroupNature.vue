<template>
    <div class="iande-stack stack-lg">
        <h1>Natureza do grupo</h1>
        <p>Escolas, ONGs, fundações e outras instituições devem preencher o campo como <b>Grupo Institucional</b>. Famílias, grupos de amigos ou turistas devem se cadastrar como <b>Outros Grupos</b>.</p>
        <div>
            <label class="iande-label" for="nature">Natureza do grupo</label>
            <Select id="nature" v-model="nature" :validations="$v.nature" :options="natureOptions"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import Select from '../components/Select.vue'
    import { constant } from '../utils'

    export default {
        name: 'GroupNature',
        components: {
            Select
        },
        computed: {
            ...sync('appointments/current@', [
                'nature',
            ]),
            natureOptions: constant([
                ['institucional', 'Grupo Institucional'],
                ['outro', 'Outros Grupos']
            ])
        },
        validations: {
            nature: { required },
        }
    }
</script>