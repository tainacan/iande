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
                this.modelValue = [...this.modelValue, this.default()]
            },
            updateItem (index) {
                return (item) => {
                    const newModelValue = this.modelValue.slice()
                    newModelValue[index] = item
                    this.modelValue = newModelValue
                }
            }
        }
    }
</script>