<template>
    <fieldset class="iande-appointments-filter iande-form" :aria-labelledby="idFor('label')">
        <div class="iande-appointments-filter__row">
            <div :id="idFor('label')" class="iande-appointments-filter__label">{{ label }}:</div>
            <template v-for="option of options">
                <input :id="idFor(option.value)" :key="`input-${option.value}`" type="radio" :name="id" :value="option.value" v-model="modelValue">
                <label :for="idFor(option.value)" :key="`label-${option.value}`">
                    <span class="iande-label" v-if="option.icon" :aria-label="option.label">
                        <Icon :icon="option.icon"/>
                    </span>
                    <span class="iande-label" v-else>{{ option.label }}</span>
                </label>
            </template>
        </div>
    </fieldset>
</template>

<script>
    export default {
        name: 'AppointmentsFilter',
        model: {
            prop: 'value',
            event: 'updateValue'
        },
        props: {
            id: { type: String, required: true },
            label: { type: String, required: true },
            options: { type: Array, required: true },
            value: { type: null, required: true },
        },
        computed: {
            modelValue: {
                get () {
                    return this.value
                },
                set (newValue) {
                    this.$emit('updateValue', newValue)
                }
            },
        },
        methods: {
            idFor (suffix) {
                return `filter-${this.id}-${suffix}`
            },
        }
    }
</script>
