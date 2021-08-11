<template>
    <div class="iande-field">
        <select :id="id" :class="classes" :aria-describedby="errorId" v-model="modelValue">
            <option :value="nullValue" disabled v-if="value === nullValue">{{ placeholder }}</option>
            <option v-for="(option, label) of normalizedOptions" :key="label" :value="option">
                {{ __(label, 'iande') }}
            </option>
        </select>
        <FormError :id="errorId" :v="v" v-if="v.$error"/>
    </div>
</template>

<script>
    import CustomField from '@mixins/CustomField'
    import { __ } from '@plugins/wp-i18n'

    export default {
        name: 'Select',
        mixins: [CustomField],
        props: {
            options: { type: [Array, Object], required: true },
            placeholder: { type: String, default: __('Selecione uma das opções', 'iande') },
        },
        computed: {
            classes () {
                return ['iande-input', this.fieldClass, this.v.$error && 'invalid']
            },
            normalizedOptions () {
                if (Array.isArray(this.options)) {
                    return Object.fromEntries(this.options.map(option => [option, option]))
                } else {
                    return this.options
                }
            },
            nullValue () {
                if (!this.value && this.value !== null) {
                    return this.value
                }
                return null
            },
            optionsLength () {
                if (Array.isArray(this.options)) {
                    return this.options.length
                } else {
                    return Object.keys(this.options).length
                }
            }
        }
    }
</script>
