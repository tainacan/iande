<template>
    <div id="iande-visit-date" class="iande-stack stack-lg">
        <h1>Reserve sua visita</h1>
        <div v-if="exhibitions.length > 1">
            <label class="iande-label" for="exhibitionId">Exibição</label>
            <Select id="exhibitionId" v-model="exhibitionId" :validations="$v.exhibitionId" :options="exhibitionOptions"/>
            <p class="iande-exhibition-description" v-if="exhibition && exhibition.description">
                {{ exhibition.description }}
            </p>
        </div>
        <div>
            <label class="iande-label" for="purpose">Qual o objetivo da visita?</label>
            <Select id="purpose" v-model="purpose" :validations="$v.purpose" :options="purposeOptions"/>
        </div>
        <div v-if="isOther(purpose)">
            <label class="iande-label" for="purposeOther">Qual?</label>
            <Input id="purposeOther" type="text" v-model="purposeOther" :validations="$v.purposeOther"/>
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
            <SlotPicker ref="slots" id="hour" :day="date" v-model="hour" :validations="$v.hour"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import DatePicker from './DatePicker.vue'
    import Input from './Input.vue'
    import Select from './Select.vue'
    import SlotPicker from './SlotPicker.vue'
    import { constant, isOther, watchForOther } from '../utils'
    import { date, time } from '../utils/validators'

    export default {
        name: 'VisitDate',
        components: {
            DatePicker,
            Input,
            Select,
            SlotPicker,
        },
        computed: {
            ...sync('appointments/current@', {
                date: 'date',
                exhibitionId: 'exhibition_id',
                hour: 'hour',
                name: 'name',
                purpose: 'purpose',
                purposeOther: 'purpose_other',
            }),
            exhibition: get('appointments/exhibition'),
            exhibitionOptions () {
                const entries = this.exhibitions.map(exhibition => [exhibition.title, exhibition.ID])
                return Object.fromEntries(entries)
            },
            exhibitions: get('exhibitions/list'),
            purposeOptions: constant(window.IandeSettings.purposes.slice(1)) // @todo Stop slicing it eventually
        },
        validations: {
            date: { date, required },
            exhibitionId: { required },
            hour: { required, time },
            name: { },
            purpose: { required },
            purposeOther: { },
        },
        watch: {
            date () {
                this.$nextTick(() => {
                    if (!this.$refs.slots.hours.includes(this.hour)) {
                        this.hour = ''
                    }
                })
            },
            exhibitions: {
                handler () {
                    if (this.exhibitions.length === 1) {
                        this.exhibitionId = this.exhibitions[0].ID
                    }
                },
                immediate: true,
            },
            purpose: watchForOther('purpose', 'purposeOther'),
        },
        methods: {
            isOther,
        }
    }
</script>
