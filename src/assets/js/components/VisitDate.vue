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
    import { constant } from '../utils'
    import { date, time } from '../utils/validators'

    export default {
        name: 'VisitDate',
        components: {
            Input,
            Select,
        },
        computed: {
            ...sync('appointments/current@', ['date', 'hour', 'name', 'purpose']),
            purposeOptions: constant([
                'Ilustrar os conteúdos que estou trabalhando com esse grupo',
                'Complementar o processo educacional realizado pela instituição de origem do grupo',
                'Possibilitar ao grupo o acesso/conhecimento à exposições e museus',
                'Promover o aprendizado sobre os temas da exposição/museu',
                'Iniciar a exploração/descoberta de um novo tema',
                'Desenvolver a cultura geral do grupo',
                'Promover uma atividade de lazer'
            ])
        },
        validations: {
            date: { date, required },
            hour: { required, time },
            name: { },
            purpose: { required },
        }
    }
</script>
