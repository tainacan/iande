<template>
    <div>
        <TheMask :mask="mask" :masked="false" v-bind="inputAttrs" @input="$emit('update:value', $event)"/>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import { TheMask } from 'vue-the-mask'

    import FormError from './FormError.vue'

    export default {
        name: 'MaskedInput',
        components: {
            FormError,
            TheMask,
        },
        inheritAttrs: false,
        model: {
            prop: 'value',
            event: 'update:value'
        },
        props: {
            id: { type: String, required: true },
            inputClass: { type: String, default: null },
            mask: { type: [Array, String], required: true },
            value: { type: null, required: true },
            validations: { type: Object, required: true }
        },
        computed: {
            errorId () {
                return `${this.id}__error`
            },
            inputAttrs () {
                return {
                    ...this.$attrs,
                    'aria-describedby': this.errorId,
                    class: ['iande-input', this.inputClass, this.validations.$error && 'invalid'],
                    id: this.id,
                    name: this.id,
                    value: this.value,
                }
            }
        }
    }
</script>