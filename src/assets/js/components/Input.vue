<template>
    <div class="iande-field">
        <input v-bind="inputAttrs" v-model="modelValue">
        <FormError :id="errorId" :v="v" v-if="v.$error"/>
    </div>
</template>

<script>
    import CustomField from '@mixins/CustomField'

    export default {
        name: 'Input',
        mixins: [CustomField],
        inheritAttrs: false,
        computed: {
            inputAttrs () {
                return {
                    ...this.$attrs,
                    'aria-describedby': this.errorId,
                    class: ['iande-input', this.fieldClass, this.v.$error && 'invalid'],
                    id: this.id,
                    name: this.id,
                }
            }
        }
    }
</script>
