<template>
    <div>
        <input class="input" :class="inputClass" :id="id" :name="id" :value="value" :aria-describedby="errorId" v-bind="$attrs" @input="$emit('update:value', $event.target.value)">
        <div class="form-error" :id="errorId" v-if="validations.$error">
            <span v-if="validations.required === false">Campo obrigatório</span>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ValidatedInput',
        inheritAttrs: false,
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
            }
        }
    }
</script>