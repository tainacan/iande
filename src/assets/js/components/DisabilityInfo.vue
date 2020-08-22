<template>
    <div class="iande-stack stack-lg">
        <div>
            <label :for="`${id}_type`" class="iande-label">Qual tipo de deficiência?</label>
            <Select :id="`${id}_type`" v-model="type" :validations="validations.type" :options="disabilityOptions"/>
        </div>
        <div>
            <label :for="`${id}_count`" class="iande-label">Quantas pessoas?</label>
            <Input :id="`${id}_count`" type="number" min="1" v-model="count" :validations="validations.count"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import Input from './Input.vue'
    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'
    import { constant, subModel } from '../utils'

    export default {
        name: 'DisabilityInfo',
        components: {
            Input,
            Select,
        },
        mixins: [CustomField],
        computed: {
            count: subModel('count'),
            disabilityOptions: constant(window.IandeSettings.deficiencies),
            type: subModel('type'),
        },
    }
</script>