<template>
    <div class="iande-field">
        <TheMask :mask="mask" :masked="false" v-bind="inputAttrs" v-model="modelValue"/>
        <FormError :id="errorId" :v="v" v-if="v.$error"/>
    </div>
</template>

<script>
    import { TheMask } from 'vue-the-mask'

    import CustomField from '@mixins/CustomField'

    export default {
        name: 'MaskedInput',
        components: {
            TheMask,
        },
        mixins: [CustomField],
        inheritAttrs: false,
        props: {
            mask: { type: [Array, String], required: true },
        },
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
