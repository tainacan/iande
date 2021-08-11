<template>
    <div class="iande-stack stack-lg">
        <div>
            <Label :for="`${id}_name`">{{ __('Qual idioma?', 'iande') }}</Label>
            <Select :id="`${id}_name`" v-model="name" :v="v.languages_name" :options="$iande.languages"/>
        </div>
        <div v-if="isOther(name)">
            <Label :for="`${id}_other`">{{ __('Especifique o idioma', 'iande') }}</Label>
            <Input :id="`${id}_other`" type="text" v-model="other" :v="v.languages_other"/>
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
        name: 'LanguageInfo',
        components: {
            Input,
            Label,
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
