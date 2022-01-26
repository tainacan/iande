<template>
    <div class="iande-field">
        <Datepicker :disabledDates="disabledDates" :format="_x('dd/MM/yyyy', 'vuejs-datepicker', 'iande')" :inputClass="inputClasses" v-bind="inputAttrs" v-model="dateValue"/>
        <FormError :id="errorId" :v="v" v-if="v.$error"/>
    </div>
</template>

<script>
    import { DateTime } from 'luxon'
    import Datepicker from 'vuejs-datepicker'
    import { get } from 'vuex-pathify'

    import CustomField from '@mixins/CustomField'
    import { getWorkingHours } from '@utils/agenda'

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
                const disabledDates = {
                    customPredictor: this.disabledDatesPredictor,
                }
                if (this.exhibition) {
                    const daysInAdvance = this.exhibition.days_advance ? Number(this.exhibition.days_advance) : 1
                    const minAdvance = DateTime
                        .fromObject({ hour: 0, minute: 0, second: 0, millisecond: 0 })
                        .plus({ days: daysInAdvance })
                        .toJSDate()
                    const startDate = DateTime.fromISO(this.exhibition.date_from).toJSDate()
                    if (startDate > minAdvance) {
                        disabledDates.to = startDate
                    } else {
                        disabledDates.to = minAdvance
                    }
                    if (this.exhibition.date_to) {
                        const endDate = DateTime.fromISO(this.exhibition.date_to).endOf('day').toJSDate()
                        disabledDates.from = endDate
                    }
                }
                return disabledDates
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
                return ['iande-input', this.fieldClass, this.v.$error && 'invalid']
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
