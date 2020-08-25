<template>
    <div class="iande-stack stack-lg">
        <h1>Reserve sua visita</h1>
        <div>
            <label class="iande-label" for="purpose">Qual o objetivo da visita?</label>
            <Select id="purpose" v-model="purpose" :validations="$v.purpose" :options="purposeOptions"/>
        </div>
        <div>
            <label class="iande-label" for="name">Dê um nome para sua visita<span class="iande-label__optional">(opcional)</span></label>
            <Input id="name" type="text" placeholder="Se quiser, atribua um nome para esta visita" v-model="name" :validations="$v.name"/>
        </div>
        <div>
            <label class="iande-label" for="date">Data da visitação</label>
            <DatePicker id="date" v-model="date" placeholder="Selecione uma data" format="dd/MM/yyyy" :validations="$v.date"/>
        </div>
        <div>
            <label class="iande-label" for="hour">Horário</label>
            <Input id="hour" type="time" placeholder="Selecione um horário disponível" v-model="hour" :validations="$v.hour"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import DatePicker from './DatePicker.vue'
    import Input from './Input.vue'
    import Select from './Select.vue'
    import { constant } from '../utils'
    import { date, time } from '../utils/validators'

    export default {
        name: 'VisitDate',
        components: {
            DatePicker,
            Input,
            Select,
        },
        computed: {
            ...sync('appointments/current@', ['date', 'hour', 'name', 'purpose']),
            purposeOptions: constant(window.IandeSettings.purposes.slice(1))
        },
        validations: {
            date: { date, required },
            hour: { required, time },
            name: { },
            purpose: { required },
        }
    }
</script>
