<template>
    <div class="iande-stack stack-lg">
        <h1>Pessoa Responsável</h1>
        <div>
            <label class="iande-label" for="is-contact">Você é o contato responsável pela visita?</label>
            <RadioGroup id="is-contact" v-model="isContact" :validations="$v.isContact" :options="binaryOptions"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import RadioGroup from '../components/RadioGroup.vue'
    import { constant } from '../utils'

    export default {
        name: 'ResponsiblePerson',
        components: {
            RadioGroup
        },
        data () {
            return {
                isContact: null
            }
        },
        computed: {
            binaryOptions: constant([[true, 'Sim'], [false, 'Não']])
        },
        validations: {
            isContact: { required },
        }

    }
</script>