<template>
    <div class="iande-field">
        <Select :id="id" :placeholder="__('Selecione um dos horários disponíveis', 'iande')" :empty="emptyMessage" v-model="modelValue" :v="v" :options="options"/>
    </div>
</template>

<script>
    import { get } from 'vuex-pathify'

    import Select from '@components/Select.vue'
    import CustomField from '@mixins/CustomField'
    import { __, sprintf } from '@plugins/wp-i18n'
    import { getSlots } from '@utils/agenda'

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
                    return __('Selecione um dia primeiro', 'iande')
                } else {
                    return __('Nenhum horário disponível', 'iande')
                }
            },
            exhibition: get('appointments/exhibition'),
            hours () {
                /* Used by GroupDate component */
                return this.availableSlots.map(slot => slot.start.toFormat('HH:mm'))
            },
            options () {
                const entries = this.availableSlots.map(slot => {
                    const start = slot.start.toFormat(__('HH:mm', 'iande'))
                    const end = slot.end.toFormat(__('HH:mm', 'iande'))
                    return [sprintf(__('%s a %s', 'iande'), start, end), start]
                })
                return Object.fromEntries(entries)
            },
        }
    }
</script>
