<template>
    <div class="iande-field">
        <Datepicker :disabledDates="disabledDates" format="dd/MM/yyyy" :inputClass="inputClasses" v-bind="inputAttrs" v-model="dateValue"/>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Datepicker from 'vuejs-datepicker'
    import { get } from 'vuex-pathify'

    import CustomField from './mixins/CustomField'
    import { getWorkingHours } from '../utils/agenda'

    const weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

    export default {
        name: 'DatePicker',
        components: {
            Datepicker,
        },
        mixins: [CustomField],
        inheritAttrs: false,
        computed: {
            disabledDatesPredictor () {
                if (!this.exhibition) {
                    return () => true
                } else {
                    return (date) => {
                        const intervals = getWorkingHours(this.exhibition, date)
                        return intervals.length === 0
                    }
                }
            },
            disabledDates () {
                const customPredictor = this.disabledDatesPredictor
                const to = DateTime.fromObject({ hour: 0, minute: 0, second: 0, millisecond: 0 }).toJSDate()
                return { customPredictor, to }
            },
            dateValue: {
                get () {
                    if (this.value) {
                        return DateTime.fromISO(this.value).toJSDate()
                    } else {
                        return null
                    }
                },
                set (newValue) {
                    this.modelValue = DateTime.fromJSDate(newValue).toISODate()
                }
            },
            exhibition: get('appointments/exhibition'),
            inputClasses () {
                return ['iande-input', this.fieldClass, this.validations.$error && 'invalid']
            },
            inputAttrs () {
                return {
                    ...this.$attrs,
                    'aria-describedby': this.errorId,
                    id: this.id,
                    name: this.id,
                }
            }
        }
    }
</script>