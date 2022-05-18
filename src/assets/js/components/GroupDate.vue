<template>
    <section class="iande-stack stack-lg">
        <h2 class="iande-group-title">{{ sprintf(__('Grupo %s:', 'iande'), n) }}</h2>
        <div>
            <Label :for="`${id}_name`">{{ __('Nome do grupo', 'iande') }}</Label>
            <Input :id="`${id}_name`" type="text" :placeholder="__('Ex.: 1° ano G - Prof. Marta', 'iande')" v-model="name" :v="v.name"/>
        </div>
        <template v-if="exhibition">
            <div>
                <Label :for="`${id}_date`">{{ __('Data da visitação', 'iande') }}</Label>
                <DatePicker :id="`${id}_date`" v-model="date" :placeholder="__('Selecione uma data', 'iande')" format="dd/MM/yyyy" :v="v.date"/>
            </div>
            <div v-if="date">
                <Label :for="`${id}_hour`">{{ __('Horário', 'iande') }}</Label>
                <SlotPicker :id="`${id}_hour`" ref="slots" :day="date" v-model="hour" :v="v.hour"/>
                <div class="iande-form-message" :class="{ '-error': availability.visitors === 0 }" v-if="availability">
                    {{ sprintf(__('Total de vagas disponíveis para o agendamento atual: %s', 'iande'), availability.visitors) }}
                </div>
            </div>
        </template>
    </section>
</template>

<script>
    import { get } from 'vuex-pathify'

    import DatePicker from '@components/DatePicker.vue'
    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import SlotPicker from '@components/SlotPicker.vue'
    import CustomField from '@mixins/CustomField'
    import { api, subModel } from '@utils'

    export default {
        name: 'GroupDate',
        components: {
            DatePicker,
            Input,
            Label,
            SlotPicker,
        },
        mixins: [CustomField],
        data () {
            return {
                availability: null,
            }
        },
        computed: {
            date: subModel('date'),
            exhibition: get('appointments/exhibition'),
            hour: subModel('hour'),
            n () {
                return Number(this.id.split('_').pop()) + 1
            },
            name: subModel('name'),
        },
        watch: {
            date () {
                this.checkAvailability()
                this.$nextTick(() => {
                    if (this.$refs.slots && !this.$refs.slots.hours.includes(this.hour)) {
                        this.hour = ''
                    }
                })
            },
            async hour () {
                this.checkAvailability()
            },
        },
        async beforeMount () {
            await this.checkAvailability()
        },
        methods: {
            async checkAvailability () {
                const { date, exhibition, hour } = this
                if (date && hour) {
                    const availability = await api.post('exhibition/check_availability', { date, hour, ID: exhibition.ID })
                    this.availability = availability
                } else {
                    this.availability = null
                }
            },
        },
    }
</script>
