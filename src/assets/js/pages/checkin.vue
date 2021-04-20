<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Check-in</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="checkin">
                <template v-if="submitted">
                    <p class="text-center">Check-in realizado com sucesso!</p>
                </template>

                <template v-else>
                    <div>
                        <label for="showed" class="iande-label">O grupo apareceu para a visita?</label>
                        <RadioGroup id="showed" v-model="form.checkin_showed" :validations="$v.form.checkin_showed" :options="binaryOptions"/>
                    </div>

                    <template v-if="showedYes">
                        <div>
                            <label for="hour" class="iande-label">Horário efetivo de início da visita</label>
                            <div class="iande-hint">A visita foi agendada para ocorrer entre <b>{{ group.hour }} - {{ endHour }}</b>. Informe se o grupo iniciou a visita no horário previsto.</div>
                            <RadioGroup id="hour" v-model="form.checkin_hour" :validations="$v.form.checkin_hour" :options="binaryOptions"/>
                        </div>

                        <div>
                            <label for="num-people" class="iande-label">Quantidade efetiva de integrantes do grupo</label>
                            <div class="iande-hint">O agendamendo considera a previsão de <b>{{ group.num_people }} pessoas</b> no total. Infome se o grupo presente condiz com informações agendadas.</div>
                            <RadioGroup id="num-people" v-model="form.checkin_num_people" :validations="$v.form.checkin_num_people" :options="binaryOptions"/>
                            <template v-if="form.checkin_num_people === 'no'">
                                <label for="num-people-actual" class="iande-hint">Quantas pessoas compareceram efetivamente?</label>
                                <Input id="num-people-actual" type="number" v-model.number="form.checkin_num_people_actual" :validations="$v.form.checkin_num_people_actual"/>
                            </template>
                        </div>

                        <div>
                            <label for="num-responsible" class="iande-label">Quantidade efetiva de responsáveis</label>
                            <div class="iande-hint">O agendamendo considera a previsão de <b>{{ group.num_responsible }} responsáveis</b>. Infome se o grupo presente condiz com informações agendadas.</div>
                            <RadioGroup id="num-responsible" v-model="form.checkin_num_responsible" :validations="$v.form.checkin_num_responsible" :options="binaryOptions"/>
                            <template v-if="form.checkin_num_responsible === 'no'">
                                <label for="num-responsible-actual" class="iande-hint">Quantos responsáveis compareceram efetivamente?</label>
                                <Input id="num-responsible-actual" type="number" v-model.number="form.checkin_num_responsible_actual" :validations="$v.form.checkin_num_responsible_actual"/>
                            </template>
                        </div>

                        <div>
                            <label for="disabilities" class="iande-label">Quantidade efetiva de pessoas com cada tipo de necessidade especial</label>
                            <div class="iande-hint">O agendamendo prevê <span v-html="disabilities"/>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="disabilities" v-model="form.checkin_disabilities" :validations="$v.form.checkin_disabilities" :options="binaryOptions"/>
                        </div>

                        <div>
                            <label for="languages" class="iande-label">Quantidade efetiva de pessoas falando outros idiomas diferentes de português</label>
                            <div class="iande-hint">O agendamendo prevê <span v-html="languages"/>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="languages" v-model="form.checkin_languages" :validations="$v.form.checkin_languages" :options="binaryOptions"/>
                        </div>

                        <div>
                            <label for="age-range" class="iande-label">Confirmação de quantidades por faixa etária</label>
                            <div class="iande-hint">O agendamendo prevê <b>{{ group.age_range.toLocaleLowerCase() }}</b>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="age-range" v-model="form.checkin_age_range" :validations="$v.form.checkin_age_range" :options="binaryOptions"/>
                        </div>
                    </template>

                    <template v-else-if="showedNo">
                        <div>
                            <label for="noshow-type" class="iande-label">A visita não foi realizada devido a</label>
                            <RadioGroup id="noshow-type" v-model="form.checkin_noshow_type" :validations="$v.form.checkin_noshow_type" :options="noshowTypeOptions"/>
                        </div>

                        <div v-if="form.checkin_noshow_type">
                            <label for="noshow-reason" class="iande-label">Qual desafio impossibilitou a visita?</label>
                            <RadioGroup id="noshow-reason" columns v-model="form.checkin_noshow_reason" :validations="$v.form.checkin_noshow_reason" :options="noshowReasonOptions"/>
                        </div>

                        <div v-if="isOther(form.checkin_noshow_reason)">
                            <label for="noshow-reason-other" class="iande-label">Qual?</label>
                            <TextArea id="noshow-reason-other" columns v-model="form.checkin_noshow_reason_other" :validations="$v.form.checkin_noshow_reason_other"/>
                        </div>
                    </template>

                    <div class="iande-stack stack-md">
                        <div class="iande-form-error" v-if="formError">
                            <span>{{ formError }}</span>
                        </div>
                        <button class="iande-button primary" type="submit">
                            Enviar
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
                form: {
                    checkin_age_range: null,
                    checkin_disabilities: null,
                    checkin_hour: null,
                    checkin_languages: null,
                    checkin_noshow_reason: '',
                    checkin_noshow_reason_other: '',
                    checkin_noshow_type: null,
                    checkin_num_people: null,
                    checkin_num_people_actual: null,
                    checkin_num_responsible: null,
                    checkin_num_responsible_actual: null,
                    checkin_showed: null,
                    has_checkin: 'on',
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
                if (!this.showedNo || !this.form.checkin_noshow_type) {
                    return []
                } else if (this.form.checkin_noshow_type === 'internal') {
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
                return this.form.checkin_showed === 'no'
            },
            showedYes () {
                return this.form.checkin_showed === 'yes'
            },
        },
        validations () {
            return {
                form: {
                    checkin_age_range: { required: requiredIf(() => this.showedYes) },
                    checkin_disabilities: { required: requiredIf(() => this.showedYes) },
                    checkin_hour: { required: requiredIf(() => this.showedYes) },
                    checkin_languages: { required: requiredIf(() => this.showedYes) },
                    checkin_noshow_reason: { required: requiredIf(() => this.showedNo) },
                    checkin_noshow_reason_other: { },
                    checkin_noshow_type: { required: requiredIf(() => this.showedNo) },
                    checkin_num_people: { required: requiredIf(() => this.showedYes) },
                    checkin_num_people_actual: { required: requiredIf(() => this.form.checkin_num_people === 'no'), numeric },
                    checkin_num_responsible: { required: requiredIf(() => this.showedYes) },
                    checkin_num_responsible_actual: { required: requiredIf(() => this.form.checkin_num_responsible === 'no'), numeric },
                    checkin_showed: { required },
                },
            }
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {
                    const group = await api.get('group/get', { ID: Number(qs.get('ID')) })
                    this.group = group
                    const exhibition = await api.get('exhibition/get', { ID: group.exhibition_id })
                    this.exhibition = exhibition
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            async checkin () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        await api.post('group/checkin_update', { ID: this.group.ID, ...this.form })
                        this.submitted = true
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
            isOther,
        },
    }
</script>
