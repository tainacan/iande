<template>
    <div class="iande-field iande-stack stack-lg">
        <template v-for="(item, i) of value">
            <slot name="item" :id="id" :onUpdate="updateItem(i)" :value="item" :validations="validations[i] || {}"/>
        </template>
        <slot name="addItem" :action="addItem"></slot>
    </div>
</template>

<script>
    import CustomField from './mixins/CustomField'

    export default {
        name: 'Repeater',
        mixins: [CustomField],
        props: {
            default: { type: Function, required: true }
        },
        watch: {
            modelValue: {
                handler () {
                    if (this.modelValue.length === 0) {
                        this.modelValue = [this.default()]
                    }
                },
                immediate: true
            }
        },
        methods: {
            addItem () {
                this.$emit('update:value', [...this.value, this.default()])
            },
            updateItem (index) {
                return (e) => {
                    const value = this.value.slice()
                    value[index] = e
                    this.$emit('update:value', value)
                }
            }
        }
    }
</script>