<template>
    <div class="iande-field">
        <select :id="id" :class="classes" :aria-describedby="errorId" v-model="modelValue">
            <option :value="nullValue" disabled v-if="value === nullValue">{{ placeholder }}</option>
            <option :value="nullValue" disabled v-if="empty && options.length === 0">{{ empty }}</option>
            <option v-for="(option, label) of normalizedOptions" :key="label" :value="option">
                {{ label }}
            </option>
        </select>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import CustomField from './mixins/CustomField'

    export default {
        name: 'Select',
        mixins: [CustomField],
        props: {
            empty: { type: String, default: null },
            options: { type: [Array, Object], required: true },
            placeholder: { type: String, default: 'Selecione uma das opções' },
        },
        computed: {
            classes () {
                return ['iande-input', this.fieldClass, this.validations.$error && 'invalid']
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
            }
        }
    }
</script>