<template>
    <div class="iande-stack stack-lg">
        <h1>Reserve sua visita</h1>
        <div>
            <label class="iande-label" for="objective">Qual o objetivo da visita?</label>
            <Select id="objective" v-model="objective" :validations="$v.objective" :options="[]"/>
        </div>
        <div>
            <label class="iande-label" for="name">Dê um nome para sua visita<span class="iande-label__optional">(opcional)</span></label>
            <Input id="name" type="text" placeholder="Se quiser, atribua um nome para esta visita" v-model="name" :validations="$v.name"/>
        </div>
        <div>
            <label class="iande-label" for="date">Data da visitação</label>
            <Input id="date" type="date" placeholder="Selecione uma data" v-model="date" :validations="$v.date"/>
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

    import Input from './Input.vue'
    import Select from './Select.vue'
    import { date, time } from '../utils/validators'

    export default {
        name: 'VisitDate',
        components: {
            Input,
            Select,
        },
        computed: {
            ...sync('appointments/current@', ['date', 'hour', 'name', 'objective'])
        },
        validations: {
            date: { date, required },
            hour: { required, time },
            name: { required },
            objective: { required },
        }
    }
</script>
