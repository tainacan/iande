<template>
    <div class="iande-stack stack-lg">
        <div>
            <Label :for="`${id}_type`">{{ __('Qual tipo de deficiência?', 'iande') }}</Label>
            <Select :id="`${id}_type`" v-model="type" :v="v.disabilities_type" :options="$iande.disabilities"/>
        </div>
        <div v-if="isOther(type)">
            <Label :for="`${id}_other`">{{ __('Especifique o tipo de deficiência', 'iande') }}</Label>
            <Input :id="`${id}_other`" type="text" v-model="other" :v="v.disabilities_other"/>
        </div>
        <div>
            <Label :for="`${id}_count`">{{ __('Quantas pessoas?', 'iande') }}</Label>
            <Input :id="`${id}_count`" type="number" min="1" v-model="count" :v="v.disabilities_count"/>
        </div>
    </div>
</template>

<script>
    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import Select from '@components/Select.vue'
    import CustomField from '@mixins/CustomField'
    import { isOther, subModel, watchForOther } from '@utils'

    export default {
        name: 'DisabilityInfo',
        components: {
            Input,
            Label,
            Select,
        },
        mixins: [CustomField],
        computed: {
            count: subModel('disabilities_count'),
            other: subModel('disabilities_other'),
            type: subModel('disabilities_type'),
        },
        watch: {
            type: watchForOther('type', 'other'),
        },
        methods: {
            isOther,
        }
    }
</script>
