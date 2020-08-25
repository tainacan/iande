<template>
    <div class="iande-field">
        <Datepicker :disabledDates="disabledDates" format="dd/MM/yyyy" :inputClass="inputClasses" v-bind="inputAttrs" v-model="dateValue"/>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Datepicker from 'vuejs-datepicker'

    import CustomField from './mixins/CustomField'

    const weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

    export default {
        name: 'DatePicker',
        components: {
            Datepicker,
        },
        mixins: [CustomField],
        inheritAttrs: false,
        computed: {
            disabledDates () {
                const to = DateTime.fromObject({ hour: 0, minute: 0, second: 0, millisecond: 0 }).toJSDate()
                const days = []
                for (let i = 0; i < 7; i++) {
                    const day = window.IandeSettings.schedules[weekDays[i]]
                    if (!day || !Array.isArray(day)) {
                        days.push(i)
                    } else if (!Boolean(day.find(interval => interval.to && interval.from))) {
                        days.push(i)
                    }
                }
                return { days, to }
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