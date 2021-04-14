<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Check-in</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="submit">
                <template v-if="submitted">
                    <p class="text-center">Check-in realizado com sucesso!</p>
                </template>

                <template v-else>
                    <div>
                        <label for="showed" class="iande-label">O grupo apareceu para a visita?</label>
                        <RadioGroup id="showed" v-model="checkin.showed" :validations="$v.checkin.showed" :options="binaryOptions"/>
                    </div>

                    <template v-if="showedYes">
                        <div>
                            <label for="hour" class="iande-label">Horário efetivo de início da visita</label>
                            <div class="iande-hint">A visita foi agendada para ocorrer entre <b>{{ group.hour }} - {{ endHour }}</b>. Informe se o grupo iniciou a visita no horário previsto.</div>
                            <RadioGroup id="hour" v-model="checkin.hour" :validations="$v.checkin.hour" :options="binaryOptions"/>
                        </div>

                        <div>
                            <label for="num-people" class="iande-label">Quantidade efetiva de integrantes do grupo</label>
                            <div class="iande-hint">O agendamendo considera a previsão de <b>{{ group.num_people }} pessoas</b> no total. Infome se o grupo presente condiz com informações agendadas.</div>
                            <RadioGroup id="num-people" v-model="checkin.num_people" :validations="$v.checkin.num_people" :options="binaryOptions"/>
                            <template v-if="checkin.num_people === 'no'">
                                <label for="num-people-actual" class="iande-hint">Quantas pessoas compareceram efetivamente?</label>
                                <Input id="num-people-actual" type="number" v-model.number="checkin.num_people_actual" :validations="$v.checkin.num_people_actual"/>
                            </template>
                        </div>

                        <div>
                            <label for="num-responsible" class="iande-label">Quantidade efetiva de responsáveis</label>
                            <div class="iande-hint">O agendamendo considera a previsão de <b>{{ group.num_responsible }} responsáveis</b>. Infome se o grupo presente condiz com informações agendadas.</div>
                            <RadioGroup id="num-responsible" v-model="checkin.num_responsible" :validations="$v.checkin.num_responsible" :options="binaryOptions"/>
                            <template v-if="checkin.num_responsible === 'no'">
                                <label for="num-responsible-actual" class="iande-hint">Quantos responsáveis compareceram efetivamente?</label>
                                <Input id="num-responsible-actual" type="number" v-model.number="checkin.num_responsible_actual" :validations="$v.checkin.num_responsible_actual"/>
                            </template>
                        </div>

                        <div>
                            <label for="disabilities" class="iande-label">Quantidade efetiva de pessoas com cada tipo de necessidade especial</label>
                            <div class="iande-hint">O agendamendo prevê <span v-html="disabilities"/>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="disabilities" v-model="checkin.disabilities" :validations="$v.checkin.disabilities" :options="binaryOptions"/>
                        </div>

                        <div>
                            <label for="languages" class="iande-label">Quantidade efetiva de pessoas falando outros idiomas diferentes de português</label>
                            <div class="iande-hint">O agendamendo prevê <span v-html="languages"/>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="languages" v-model="checkin.languages" :validations="$v.checkin.languages" :options="binaryOptions"/>
                        </div>

                        <div>
                            <label for="age-range" class="iande-label">Confirmação de quantidades por faixa etária</label>
                            <div class="iande-hint">O agendamendo prevê <b>{{ group.age_range.toLocaleLowerCase() }}</b>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="age-range" v-model="checkin.age_range" :validations="$v.checkin.age_range" :options="binaryOptions"/>
                        </div>
                    </template>

                    <template v-else-if="showedNo">
                        <div>
                            <label for="noshow-type" class="iande-label">A visita não foi realizada devido a</label>
                            <RadioGroup id="noshow-type" v-model="checkin.noshow_type" :validations="$v.checkin.noshow_type" :options="noshowTypeOptions"/>
                        </div>

                        <div v-if="checkin.noshow_type">
                            <label for="noshow-reason" class="iande-label">Qual desafio impossibilitou a visita?</label>
                            <RadioGroup id="noshow-reason" columns v-model="checkin.noshow_reason" :validations="$v.checkin.noshow_reason" :options="noshowReasonOptions"/>
                        </div>

                        <div v-if="isOther(checkin.noshow_reason)">
                            <label for="noshow-reason-other" class="iande-label">Qual?</label>
                            <TextArea id="noshow-reason-other" columns v-model="checkin.noshow_reason_other" :validations="$v.checkin.noshow_reason_other"/>
                        </div>
                    </template>

                    <div class="iande-form-grid">
                        <div/>

                        <button class="iande-button primary" type="submit">
                            Avançar
                            <Icon icon="angle-right"/>
                        </button>
                    </div>
                </template>
            </form>
        </div>
    </article>
</template>

<script>
    import { DateTime } from 'luxon'
    import { numeric, required, requiredIf } from 'vuelidate/lib/validators'

    import Input from '../components/Input.vue'
    import RadioGroup from '../components/RadioGroup.vue'
    import TextArea from '../components/TextArea.vue'
    import { api, constant, isOther, joinMany } from '../utils'

    export default {
        name: 'CheckinPage',
        components: {
            Input,
            RadioGroup,
            TextArea,
        },
        data () {
            return {
                checkin: {
                    age_range: null,
                    disabilities: null,
                    hour: null,
                    languages: null,
                    noshow_reason: '',
                    noshow_reason_other: '',
                    noshow_type: null,
                    num_people: null,
                    num_people_actual: null,
                    num_responsible: null,
                    num_responsible_actual: null,
                    showed: null,
                },
                formError: '',
                exhibition: null,
                group: null,
                submitted: false,
            }
        },
        computed: {
            binaryOptions: constant({ 'Não': 'no', 'Sim': 'yes' }),
            disabilities () {
                const disabilities = this.group.disabilities
                if (disabilities.length === 0) {
                    return '<b>nenhuma pessoa com necessidade especial</b>'
                } else {
                    return joinMany(disabilities.map(disability => {
                        let dis = `<b>${disability.disabilities_count} pessoa${disability.disabilities_count ? 's' : ''}`
                        if (isOther(disability.disabilities_type) && disability.disabilities_other) {
                            dis += ` com ${disability.disabilities_other}</b>`
                        } else {
                            dis += ` com ${disability.disabilities_type}</b>`
                        }
                        return dis
                    }))
                }
            },
            endHour () {
                const delta = { minutes: Number(this.exhibition.duration) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat('HH:mm')
            },
            languages () {
                const languages = this.group.languages
                if (languages.length === 0) {
                    return '<b>apenas português</b>'
                } else {
                    return joinMany(languages.map(language => {
                        if (isOther(language.languages_name) && language.languages_other) {
                            return `<b>${language.languages_other.toLocaleLowerCase()}</b>`
                        } else {
                            return `<b>${language.languages_name.toLocaleLowerCase()}</b>`
                        }
                    }))
                }
            },
            noshowReasonOptions () {
                if (!this.showedNo || !this.checkin.noshow_type) {
                    return []
                } else if (this.checkin.noshow_type === 'internal') {
                    return [
                        'Exposição fechada por problemas de infraestrutura ou queda de energia',
                        'Não havia educador disponível para realizar a visita',
                        'A exposição estava com excesso de grupos',
                    ]
                } else {
                    return [
                        'Problemas de deslocamento até a exposição/museu (trânsito, endereço errado, atraso do ônibus, atraso de alunos responsáveis)',
                        'O grupo preferiu visitar a exposição sem a presença do educador',
                        'O grupo optou por realizar outra atividade na instituição',
                        'A visita foi cancelada no mesmo dia',
                        'Não sei',
                        'Outro'
                    ]
                }
            },
            noshowTypeOptions: constant({ 'Problemas internos': 'internal', 'Problemas da instituição visitante': 'visitor' }),
            showedNo () {
                return this.checkin.showed === 'no'
            },
            showedYes () {
                return this.checkin.showed === 'yes'
            },
        },
        validations () {
            let v = {
                checkin: {
                    age_range: { required: requiredIf(() => this.showedYes) },
                    disabilities: { required: requiredIf(() => this.showedYes) },
                    hour: { required: requiredIf(() => this.showedYes) },
                    languages: { required: requiredIf(() => this.showedYes) },
                    noshow_reason: { required: requiredIf(() => this.showedNo) },
                    noshow_reason_other: { },
                    noshow_type: { required: requiredIf(() => this.showedNo) },
                    num_people: { required: requiredIf(() => this.showedYes) },
                    num_people_actual: { required: requiredIf(() => this.checkin.num_people === 'no'), numeric },
                    num_responsible: { required: requiredIf(() => this.showedYes) },
                    num_responsible_actual: { required: requiredIf(() => this.checkin.num_responsible === 'no'), numeric },
                    showed: { required },
                },
            }
            console.log(v)
            return v
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {
                    const group = await api.get('group/get', { ID: Number(qs.get('ID')) })
                    this.group = { ...this.group, ...group }
                    const exhibition = await api.get('exhibition/get', { ID: group.exhibition_id })
                    this.exhibition = exhibition
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            isOther,
            async submit () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        this.submitted = true
                        console.log(this.checkin)
                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        },
    }
</script>
