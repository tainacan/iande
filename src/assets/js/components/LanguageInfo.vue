<template>
    <div class="iande-stack stack-lg">
        <div>
            <label :for="`${id}_name`" class="iande-label">{{ __('Qual idioma?', 'iande') }}</label>
            <Select :id="`${id}_name`" v-model="name" :validations="validations.languages_name" :options="$iande.languages"/>
        </div>
        <div v-if="isOther(name)">
            <label :for="`${id}_other`" class="iande-label">{{ __('Especifique o idioma', 'iande') }}</label>
            <Input :id="`${id}_other`" type="text" v-model="other" :validations="validations.languages_other"/>
        </div>
    </div>
</template>

<script>
    import Input from './Input.vue'
    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'
    import { isOther, subModel, watchForOther } from '../utils'

    export default {
        name: 'LanguageInfo',
        components: {
            Input,
            Select,
        },
        mixins: [CustomField],
        computed: {
            name: subModel('languages_name'),
            other: subModel('languages_other'),
        },
        watch: {
            name: watchForOther('name', 'other'),
        },
        methods: {
            isOther,
        }
    }
</script>
