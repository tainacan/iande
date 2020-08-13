<template>
    <div>
        <select class="iande-input" :id="id" :class="fieldClass" :aria-describedby="errorId" v-model="modelValue">
            <option :value="null" disabled v-if="value === null">{{ placeholder }}</option>
            <option :value="null" disabled v-if="empty && options.length === 0">{{ empty }}</option>
            <option v-for="option of options" :key="option[0]" :value="option[0]">
                {{ option[1] }}
            </option>
        </select>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import CustomField from './mixins/CustomField'

    export default {
        name: 'Select',
        mixins: [CustomField],
        props: {
            empty: { type: String, default: null },
            options: { type: Array, required: true },
            placeholder: { type: String, default: 'Selecione uma das opções' },
        },
        computed: {
            classes () {
                return ['iande-input', this.fieldClass, this.validations.$error && 'invalid']
            },
        }
    }
</script>