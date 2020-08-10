<template>
    <div>
        <MaskedInput :mask="mask" :masked="false" v-bind="inputAttrs" @input="$emit('update:value', $event)"/>
    </div>
</template>

<script>
    import { TheMask } from 'vue-the-mask'

    export default {
        name: 'ValidatedMaskedInput',
        components: {
            MaskedInput: TheMask,
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
            validations: { type: Object, default: () => {} }
        },
        computed: {
            errorId () {
                return `${this.id}__error`
            },
            inputAttrs () {
                return {
                    ...this.$attrs,
                    'aria-describedby': this.errorId,
                    class: ['input', this.inputClass, this.validations.$error && 'invalid'],
                    id: this.id,
                    name: this.id,
                    value: this.value,
                }
            }
        }
    }
</script>