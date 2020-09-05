<template>
    <div class="iande-field">
        <Select :id="id" placeholder="Selecione um horário disponível" :empty="emptyMessage" v-model="modelValue" :validations="validations" :options="options"/>
    </div>
</template>

<script>
    import { DateTime, Interval } from 'luxon'
    import { get } from 'vuex-pathify'

    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'
    import { getSlots } from '../utils/agenda'

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
                return getSlots(this.exhibition, this.day)
            },
            emptyMessage () {
                if (!this.day) {
                    return 'Selecione um dia primeiro'
                } else {
                    return 'Nenhum horário disponível'
                }
            },
            exhibition: get('appointments/exhibition'),
            hours () {
                /* Used by VisitDate component */
                return this.availableSlots.map(slot => slot.start.toFormat('HH:mm'))
            },
            options () {
                const entries = this.availableSlots.map(slot => {
                    const start = slot.start.toFormat('HH:mm')
                    const end = slot.end.toFormat('HH:mm')
                    return [`${start} a ${end}`, start]
                })
                return Object.fromEntries(entries)
            },
        }
    }
</script>
