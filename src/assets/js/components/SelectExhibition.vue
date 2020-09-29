<template>
    <div id="iande-visit-date" class="iande-stack stack-lg">
        <h1>Sobre a visita</h1>
        <div>
            <label class="iande-label" for="purpose">Qual o objetivo da visita?</label>
            <Select id="purpose" v-model="purpose" :validations="$v.purpose" :options="purposeOptions"/>
        </div>
        <div v-if="isOther(purpose)">
            <label class="iande-label" for="purposeOther">Especifique o objetivo da visita</label>
            <Input id="purposeOther" type="text" v-model="purposeOther" :validations="$v.purposeOther"/>
        </div>
        <div>
            <label class="iande-label" for="name">Dê um nome à visita<span class="iande-label__optional">(opcional)</span></label>
            <Input id="name" type="text" placeholder="Se quiser, atribua um nome para esta visita" v-model="name" :validations="$v.name"/>
        </div>
        <div v-if="exhibitions.length > 1">
            <label class="iande-label" for="exhibitionId">Qual exposição será visitada?</label>
            <Select id="exhibitionId" v-model="exhibitionId" :validations="$v.exhibitionId" :options="exhibitionOptions"/>
            <p class="iande-exhibition-description" v-if="exhibition && exhibition.description">
                {{ exhibition.description }}
            </p>
        </div>
        <div>
            <label class="iande-label" for="numPeople">Quantidade prevista de pessoas</label>
            <Input id="numPeople" type="number" min="5" placeholder="Mínimo de 5 pessoas" :disabled="groupsCreated" v-model.number="numPeople" :validations="$v.numPeople"/>
            <p class="text-sm">Caso a quantidade seja maior do que a capacidade de atendimento, mais grupos serão criados automaticamente</p>
        </div>
    </div>
</template>

<script>
    import { integer, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import Input from './Input.vue'
    import Select from './Select.vue'
    import { constant, isOther, watchForOther } from '../utils'

    export default {
        name: 'SelectExhibition',
        components: {
            Input,
            Select,
        },
        computed: {
            ...sync('appointments/current@', {
                exhibitionId: 'exhibition_id',
                name: 'name',
                numPeople: 'num_people',
                purpose: 'purpose',
                purposeOther: 'purpose_other',
            }),
            exhibition: get('appointments/exhibition'),
            exhibitionOptions () {
                const entries = this.exhibitions.map(exhibition => [exhibition.title, exhibition.ID])
                return Object.fromEntries(entries)
            },
            exhibitions: get('exhibitions/list'),
            groups: get('appointments/current@groups'),
            groupsCreated () {
                return this.groups.filter(group => Boolean(group.ID)).length > 0
            },
            purposeOptions: constant(window.IandeSettings.purposes)
        },
        validations: {
            exhibitionId: { required },
            name: { },
            numPeople: { integer, required },
            purpose: { required },
            purposeOther: { },
        },
        watch: {
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