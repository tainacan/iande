<template>
    <section class="iande-stack stack-lg">
        <h2 class="iande-group-title">{{ sprintf(__('Grupo %s:', 'iande'), n) }}</h2>
        <div>
            <label class="iande-label" :for="`${id}_name`">{{ __('Nome do grupo', 'iande') }}</label>
            <Input :id="`${id}_name`" type="text" :placeholder="__('Ex.: 1° ano G - Prof. Marta', 'iande')" v-model="name" :validations="validations.name"/>
        </div>
        <template v-if="exhibition">
            <div>
                <label class="iande-label" :for="`${id}_date`">{{ __('Data da visitação', 'iande') }}</label>
                <DatePicker :id="`${id}_date`" v-model="date" :placeholder="__('Selecione uma data', 'iande')" format="dd/MM/yyyy" :validations="validations.date"/>
            </div>
            <div v-if="date">
                <label class="iande-label" :for="`${id}_hour`">{{ __('Horário', 'iande') }}</label>
                <SlotPicker :id="`${id}_hour`" ref="slots" :day="date" v-model="hour" :validations="validations.hour"/>
            </div>
        </template>
    </section>
</template>

<script>
    import { get } from 'vuex-pathify'

    import DatePicker from '@components/DatePicker.vue'
    import Input from '@components/Input.vue'
    import SlotPicker from '@components/SlotPicker.vue'
    import CustomField from '@mixins/CustomField'
    import { subModel } from '@utils'

    export default {
        name: 'GroupDate',
        components: {
            DatePicker,
            Input,
            SlotPicker,
        },
        mixins: [CustomField],
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
                this.$nextTick(() => {
                    if (this.$refs.slots && !this.$refs.slots.hours.includes(this.hour)) {
                        this.hour = ''
                    }
                })
            }
        }
    }
</script>