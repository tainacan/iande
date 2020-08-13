import FormError from '../FormError.vue'

export default {
    components: {
        FormError,
    },
    model: {
        prop: 'value',
        event: 'update:value'
    },
    props: {
        fieldClass: { type: String, default: null },
        id: { type: String, required: true },
        value: { type: null, required: true },
        validations: { type: Object, required: true }
    },
    computed: {
        errorId () {
            return `${this.id}__error`
        },
        modelValue: {
            get () {
                return this.value
            },
            set (newValue) {
                this.$emit('update:value', newValue)
            }
        }
    }
}