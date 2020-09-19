<template>
    <div class="iande-field iande-stack stack-lg">
        <div class="iande-repetition" :class="fieldClass" v-for="(item, i) of value" :key="i">
            <div class="iande-repetition__remove" v-if="i > 0" aria-label="Remover item" role="button" tabindex="0" @click="removeItem(i)">
                <Icon icon="times"/>
            </div>
            <slot name="item" :id="`${id}_${i}`" :onUpdate="updateItem(i)" :value="item" :validations="validations.$each[i]"/>
        </div>
        <slot name="addItem" :action="addItem"></slot>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import CustomField from './mixins/CustomField'

    export default {
        name: 'Repeater',
        components: {
            Icon: FontAwesomeIcon,
        },
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
            removeItem (index) {
                const newModelValue = this.modelValue.slice()
                this.modelValue = [...newModelValue.slice(0, index), ...newModelValue.slice(index + 1)].map((item, i) => {
                    if (item.id != null) {
                        return { ...item, id: i + 1 }
                    } else {
                        return item
                    }
                })
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