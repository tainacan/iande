<template>
    <div class="iande-field">
        <div class="iande-radio-group" :id="id" :class="{ columns, fieldClass }" :aria-describedby="errorId">
            <label class="iande-radio" v-for="(option, label) of normalizedOptions" :key="label">
                <input type="checkbox" :name="id" :value="option" v-model="modelValue">
                <span>{{ __(label, 'iande') }}</span>
            </label>
        </div>
        <FormError :id="errorId" :v="v" v-if="v.$error"/>
    </div>
</template>

<script>
    import CustomField from '@mixins/CustomField'

    export default {
        name: 'CheckboxGroup',
        mixins: [CustomField],
        props: {
            columns: { type: Boolean, default: false },
            options: { type: [Array, Object], required: true },
        },
        computed: {
            normalizedOptions () {
                if (Array.isArray(this.options)) {
                    return Object.fromEntries(this.options.map(option => [option, option]))
                } else {
                    return this.options
                }
            }
        }
    }
</script>
