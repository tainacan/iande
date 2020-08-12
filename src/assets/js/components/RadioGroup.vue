<template>
    <div>
        <div class="iande-radio-group" :id="id" :class="fieldClass" role="radiogroup" :aria-describedby="errorId">
            <label class="iande-radio" v-for="option of options" :key="option[1]">
                <input type="radio" :name="id" :value="option[0]" :checked="value" @change="$emit('update:value', option[0])">
                <span>{{ option[1] }}</span>
            </label>
        </div>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import CustomField from './mixins/CustomField'

    export default {
        name: 'RadioGroup',
        mixins: [CustomField],
        props: {
            options: { type: Array, required: true },
        }
    }
</script>