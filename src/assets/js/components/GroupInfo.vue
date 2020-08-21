<template>
    <div class="iande-stack stack-lg">
        <div>
            <label :for="`${id}_name`" class="iande-label">Nome do grupo</label>
            <Input :id="`${id}_name`" type="text" placeholder="Ex.: 1° ano G - Prof. Marta" v-model="name" :validations="$v.name"/>
        </div>
        <div>
            <label :for="`${id}_numPeople`" class="iande-label">Quantidade prevista de pessoas</label>
            <Input :id="`${id}_numPeople`" type="number" min="5" placeholder="Mínimo de 5 pessoas" v-model.number="numPeople" :validations="$v.numPeople"/>
            <p class="text-sm">Caso seu grupo seja maior do que a capacidade de atendimento do museu, adicione outro grupo no fim da página.</p>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import Input from './Input.vue'
    import Repeater from './Repeater.vue'
    import CustomField from './mixins/CustomField'
    import { constant, subModel } from '../utils'

    export default {
        name: 'GroupInfo',
        components: {
            Input,
            Repeater,
        },
        mixins: [CustomField],
        computed: {
            name: subModel('name'),
            numPeople: subModel('num_people'),
        },
        validations: {
            name: { required },
            numPeople: { required },
        }
    }
</script>