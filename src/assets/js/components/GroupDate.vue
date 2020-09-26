<template>
    <div class="iande-stack stack-lg">
        <h2 class="iande-group-title">Grupo {{ n }}:</h2>
        <div>
            <label class="iande-label" :for="`${id}_name`">Nome do grupo</label>
            <Input :id="`${id}_name`" type="text" placeholder="Ex.: 1° ano G - Prof. Marta" v-model="name" :validations="validations.name"/>
        </div>
        <template v-if="exhibition">
            <div>
                <label class="iande-label" :for="`${id}_date`">Data da visitação</label>
                <DatePicker :id="`${id}_date`" v-model="date" placeholder="Selecione uma data" format="dd/MM/yyyy" :validations="$v.date"/>
            </div>
            <div v-if="date">
                <label class="iande-label" :for="`${id}_hour`">Horário</label>
                <SlotPicker :id="`${id}_hour`" ref="slots" :day="date" v-model="hour" :validations="$v.hour"/>
            </div>
        </template>
    </div>
</template>

<script>
    import { get } from 'vuex-pathify'

    import DatePicker from './DatePicker.vue'
    import Input from './Input.vue'
    import SlotPicker from './SlotPicker.vue'
    import CustomField from './mixins/CustomField'
    import { constant, subModel } from '../utils'

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
                return this.id.split('_').pop()
            },
            name: subModel('name'),
        },
        watch: {
            date () {
                this.$nextTick(() => {
                    if (!this.$refs.slots.hours.includes(this.hour)) {
                        this.hour = ''
                    }
                })
            }
        }
    }
</script>