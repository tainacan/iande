<template>
    <div>
        <input v-bind="inputAttrs" @input="$emit('update:value', $event.target.value)">
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import FormError from './FormError.vue'

    export default {
        name: 'Input',
        inheritAttrs: false,
        components: {
            FormError,
        },
        model: {
            prop: 'value',
            event: 'update:value'
        },
        props: {
            id: { type: String, required: true },
            inputClass: { type: String, default: null },
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
                    class: ['iande-input', this.inputClass, this.validations.$error && 'invalid'],
                    id: this.id,
                    name: this.id,
                    value: this.value,
                }
            }
        }
    }
</script>