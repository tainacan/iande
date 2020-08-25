<template>
    <div class="iande-field">
        <Select :id="id" placeholder="Selecione um horário disponível" :empty="emptyMessage" v-model="modelValue" :validations="validations" :options="options"/>
    </div>
</template>

<script>
    import { DateTime, Interval } from 'luxon'

    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'

    const weekDays = ['', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']

    export default {
        name: 'SlotPicker',
        components: {
            Select,
        },
        mixins: [CustomField],
        props: {
            day: { type: String, required: true },
        },
        computed: {
            availableSlots () {
                if (!this.day) {
                    return []
                }
                const day = window.IandeSettings.schedules[this.weekDay]
                return day.flatMap(interval => {
                    if (interval.to && interval.from) {
                        return Interval.fromDateTimes(
                            DateTime.fromFormat(interval.from, 'HH:mm'),
                            DateTime.fromFormat(interval.to, 'HH:mm')
                        ).splitBy({ minutes: Number(window.IandeSettings.duration) })
                    } else {
                        return []
                    }
                })
            },
            emptyMessage () {
                if (!this.day) {
                    return 'Selecione um dia primeiro'
                } else {
                    return 'Nenhum horário disponível'
                }
            },
            hours () {
                /* Used by VisitDate component */
                return this.availableSlots.map(slot => slot.start.toFormat('HH:mm'))
            },
            options () {
                const entries = this.availableSlots.map(slot => {
                    const start = slot.start.toFormat('HH:mm')
                    const end = slot.end.toFormat('HH:mm')
                    return [`${start} - ${end}`, start]
                })
                return Object.fromEntries(entries)
            },
            weekDay () {
                return weekDays[DateTime.fromISO(this.day).weekday]
            }
        }
    }
</script>
