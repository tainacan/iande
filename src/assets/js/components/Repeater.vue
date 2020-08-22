<template>
    <div class="iande-field iande-stack stack-lg">
        <template v-for="(item, i) of value">
            <slot name="item" :id="`${id}_${i}`" :onUpdate="updateItem(i)" :value="item" :validations="(validations.$each && validations.$each[i]) || {}"/>
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
            factory: { type: Function, required: true }
        },
        watch: {
            modelValue: {
                handler () {
                    if (this.modelValue.length === 0) {
                        this.modelValue = [this.factory()]
                    }
                },
                immediate: true
            }
        },
        methods: {
            addItem () {
                this.modelValue = [...this.modelValue, this.factory()]
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