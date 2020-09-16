<template>
    <div class="iande-stack stack-lg">
        <div>
            <label :for="`${id}_type`" class="iande-label">Qual tipo de deficiência?</label>
            <Select :id="`${id}_type`" v-model="type" :validations="validations.type" :options="disabilityOptions"/>
        </div>
        <div v-if="isOther(type)">
            <label :for="`${id}_other`" class="iande-label">Especifique o tipo de deficiência</label>
            <Input :id="`${id}_other`" type="text" v-model="other" :validations="validations.other"/>
        </div>
        <div>
            <label :for="`${id}_count`" class="iande-label">Quantas pessoas?</label>
            <Input :id="`${id}_count`" type="number" min="1" v-model="count" :validations="validations.count"/>
        </div>
    </div>
</template>

<script>
    import Input from './Input.vue'
    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'
    import { constant, isOther, subModel, watchForOther } from '../utils'

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
            other: subModel('other'),
            type: subModel('type'),
        },
        watch: {
            type: watchForOther('type', 'other'),
        },
        methods: {
            isOther,
        }
    }
</script>