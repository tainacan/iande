<template>
    <div>
        <div class="iande-radio-group" :id="id" :class="inputClass" role="radiogroup" :aria-describedby="errorId">
            <label class="iande-radio" v-for="option of options" :key="option[0]">
                <input type="radio" :name="id" :value="option[0]" :checked="value" @change="$emit('update:value', option[0])">
                <span>{{ option[1] }}</span>
            </label>
        </div>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import FormError from './FormError.vue'

    export default {
        name: 'RadioGroup',
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
            options: { type: Array, required: true },
            value: { type: null, required: true },
            validations: { type: Object, required: true }
        },
        computed: {
            errorId () {
                return `${this.id}__error`
            }
        }
    }
</script>