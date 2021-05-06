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
                        </div>
                        <div v-if="form.checkin_num_people === 'no'">
                            <label for="num-people-actual" class="iande-hint">Quantas pessoas compareceram efetivamente?</label>
                            <Input id="num-people-actual" type="number" v-model.number="form.checkin_num_people_actual" :validations="$v.form.checkin_num_people_actual"/>
                        </div>

                        <div>
                            <label for="num-responsible" class="iande-label">Quantidade efetiva de responsáveis</label>
                            <div class="iande-hint">O agendamendo considera a previsão de <b>{{ group.num_responsible }} responsáveis</b>. Infome se o grupo presente condiz com informações agendadas.</div>
                            <RadioGroup id="num-responsible" v-model="form.checkin_num_responsible" :validations="$v.form.checkin_num_responsible" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_num_responsible === 'no'">
                            <label for="num-responsible-actual" class="iande-hint">Quantos responsáveis compareceram efetivamente?</label>
                            <Input id="num-responsible-actual" type="number" v-model.number="form.checkin_num_responsible_actual" :validations="$v.form.checkin_num_responsible_actual"/>
                        </div>

                        <div>
                            <label for="disabilities" class="iande-label">Quantidade efetiva de pessoas com cada tipo de necessidade especial</label>
                            <div class="iande-hint">O agendamendo prevê <span v-html="disabilities"/>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="disabilities" v-model="form.checkin_disabilities" :validations="$v.form.checkin_disabilities" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_disabilities === 'no'">
                            <label for="disabilities-actual" class="iande-hit">Quantas pessoas com necessidades especiais apareceram efetivamente?</label>
                            <div class="iande-complex-field">
                                <Repeater id="disabilities-actual" v-model="form.checkin_disabilities_actual" :factory="newDisability" :validations="$v.form.checkin_disabilities_actual">
                                    <template #item="{ id, onUpdate, validations, value }">
                                        <div :key="id">
                                            <DisabilityInfo :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
                                        </div>
                                    </template>
                                    <template #addItem="{ action }">
                                        <div class="iande-add-item" role="button" tabindex="0" @click="action">
                                            <span><Icon icon="plus-circle"/></span>
                                            <div class="iande-label">Adicionar necessidade especial</div>
                                        </div>
                                    </template>
                                </Repeater>
                            </div>
                        </div>

                        <div>
                            <label for="languages" class="iande-label">Quantidade efetiva de pessoas falando outros idiomas diferentes de português</label>
                            <div class="iande-hint">O agendamendo prevê <span v-html="languages"/>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="languages" v-model="form.checkin_languages" :validations="$v.form.checkin_languages" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_languages === 'no'">
                            <label for="languages-actual" class="iande-hit">Quais os idiomas falados pelo grupo efetivamente?</label>
                            <div class="iande-complex-field">
                                <Repeater id="languages-actual" v-model="form.checkin_languages_actual" :factory="newLanguage" :validations="$v.form.checkin_languages_actual">
                                    <template #item="{ id, onUpdate, validations, value }">
                                        <div :key="id">
                                            <LanguageInfo :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
                                        </div>
                                    </template>
                                    <template #addItem="{ action }">
                                        <div class="iande-add-item" role="button" tabindex="0" @click="action">
                                            <span><Icon icon="plus-circle"/></span>
                                            <div class="iande-label">Adicionar idioma</div>
                                        </div>
                                    </template>
                                </Repeater>
                            </div>
                        </div>

                        <div>
                            <label for="scholarity" class="iande-label">Confirmação de escolaridade</label>
                            <div class="iande-hint">O agendamendo prevê <b>{{ group.scholarity }}</b>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="scholarity" v-model="form.checkin_scholarity" :validations="$v.form.checkin_scholarity" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_scholarity === 'no'">
                            <label for="scholarity-actual" class="iande-hint">Qual a escolaridade do grupo efetivamente?</label>
                            <Select id="scholarity-actual" v-model="form.checkin_scholarity_actual" :validations="$v.form.checkin_scholarity_actual" :options="$iande.scholarity"/>
                        </div>

                        <div>
                            <label for="age-range" class="iande-label">Confirmação de faixa etária</label>
                            <div class="iande-hint">O agendamendo prevê <b>{{ group.age_range.toLocaleLowerCase() }}</b>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="age-range" v-model="form.checkin_age_range" :validations="$v.form.checkin_age_range" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_age_range === 'no'">
                            <label for="age-actual-actual" class="iande-hint">Qual a faixa etária efetivamente?</label>
                            <Select id="age-range-actual" v-model="form.checkin_age_range_actual" :validations="$v.form.checkin_age_range_actual" :options="$iande.ageRanges"/>
                        </div>

                        <div>
                            <label for="institutional" class="iande-label">O grupo é institucional?</label>
                            <RadioGroup id="institutional" v-model="form.checkin_institutional" :validations="$v.form.checkin_institutional" :options="binaryOptions"/>
                        </div>

                        <div v-if="form.checkin_institutional === 'yes'">
                            <label for="institution" class="iande-label">Tipo / perfil da instituição</label>
                            <div class="iande-hint" v-if="institution && appointment.group_nature === 'institutional'">O agendamendo prevê <b>{{ institution.profile }}</b>. Infome se o grupo presente condiz com informações do agendamento.</div>
                            <RadioGroup id="institution" v-model="form.checkin_institution" :validations="$v.form.checkin_institution" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_institution === 'no'">
                            <label for="institution-actual" class="iande-hint">Qual o perfil da instituição efetivamente?</label>
                            <Select id="institution-actual" v-model="form.checkin_institution_actual" :validations="$v.form.checkin_institution_actual" :options="$iande.profiles"/>
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
                            <template v-if="isOther(form.checkin_noshow_reason)">
                                <label for="noshow-reason-other" class="iande-label">Qual?</label>
                                <TextArea id="noshow-reason-other" columns v-model="form.checkin_noshow_reason_other" :validations="$v.form.checkin_noshow_reason_other"/>
                            </template>
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
    import { integer, numeric, required, requiredIf } from 'vuelidate/lib/validators'

    import DisabilityInfo from '../components/DisabilityInfo.vue'
    import Input from '../components/Input.vue'
    import LanguageInfo from '../components/LanguageInfo.vue'
    import RadioGroup from '../components/RadioGroup.vue'
    import Repeater from '../components/Repeater.vue'
    import Select from '../components/Select.vue'
    import TextArea from '../components/TextArea.vue'
    import { api, constant, isOther, joinMany } from '../utils'

    export default {
        name: 'CheckinPage',
        components: {
            DisabilityInfo,
            Input,
            LanguageInfo,
            RadioGroup,
            Repeater,
            Select,
            TextArea,
        },
        data () {
            return {
                appointment: null,
                form: {
                    checkin_age_range: null,
                    checkin_age_range_actual: null,
                    checkin_disabilities: null,
                    checkin_disabilities_actual: [],
                    checkin_hour: null,
                    checkin_institutional: null,
                    checkin_institution: null,
                    checkin_institution_actual: null,
                    checkin_languages: null,
                    checkin_languages_actual: [],
                    checkin_noshow_reason: '',
                    checkin_noshow_reason_other: '',
                    checkin_noshow_type: null,
                    checkin_num_people: null,
                    checkin_num_people_actual: null,
                    checkin_num_responsible: null,
                    checkin_num_responsible_actual: null,
                    checkin_scholarity: null,
                    checkin_scholarity_actual: null,
                    checkin_showed: null,
                    has_checkin: 'on',
                },
                formError: '',
                exhibition: null,
                group: null,
                institution: null,
                submitted: false,
            }
        },
        computed: {
            binaryOptions: constant({ 'Não': 'no', 'Sim': 'yes' }),
            disabilities () {
                const disabilities = this.group.disabilities
                if (!disabilities || disabilities.length === 0) {
                    return '<b>nenhuma</b> pessoa com necessidade especial'
                } else {
                    return joinMany(disabilities.map(disability => {
                        let dis = '<b>'
                        if (isOther(disability.disabilities_type) && disability.disabilities_other) {
                            dis += disability.disabilities_other
                        } else {
                            dis += disability.disabilities_type
                        }
                        dis += `</b> (<b>${disability.disabilities_count}</b> pessoa${disability.disabilities_count ? 's' : ''})`
                        return dis
                    }))
                }
            },
            endHour () {
                const delta = { minutes: Number(this.exhibition?.duration ?? 30) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat('HH:mm')
            },
            languages () {
                const languages = this.group.languages
                if (!languages || languages.length === 0) {
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
                    checkin_age_range: { required: requiredIf('showedYes') },
                    checkin_age_range_actual: { },
                    checkin_disabilities: { required: requiredIf('showedYes') },
                    checkin_disabilities_actual: {
                        $each: {
                            disabilities_count: { integer, required },
                            disabilities_other: { },
                            disabilities_type: { required },
                        },
                    },
                    checkin_hour: { required: requiredIf('showedYes') },
                    checkin_institution: { required: requiredIf(() => this.form.checkin_institutional === 'yes') },
                    checkin_institution_actual: { },
                    checkin_institutional: { required: requiredIf('showedYes') },
                    checkin_languages: { required: requiredIf('showedYes') },
                    checkin_languages_actual: {
                        $each: {
                            languages_name: { required },
                            languages_other: { },
                        },
                    },
                    checkin_noshow_reason: { required: requiredIf('showedNo') },
                    checkin_noshow_reason_other: { },
                    checkin_noshow_type: { required: requiredIf('showedNo') },
                    checkin_num_people: { required: requiredIf('showedYes') },
                    checkin_num_people_actual: { required: requiredIf(() => this.form.checkin_num_people === 'no'), numeric },
                    checkin_num_responsible: { required: requiredIf('showedYes') },
                    checkin_num_responsible_actual: { required: requiredIf(() => this.form.checkin_num_responsible === 'no'), numeric },
                    checkin_scholarity: { required: requiredIf('showedYes') },
                    checkin_scholarity_actual: { },
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
                    if (group.has_checkin === 'on') {
                        this.mergeCheckins()
                    }
                    const appointment = await api.get('appointment/get', { ID: group.appointment_id })
                    this.appointment = appointment
                    const [exhibition, institution] = await Promise.all([
                        api.get('exhibition/get', { ID: group.exhibition_id }),
                        appointment.institution_id ? api.get('institution/get', { ID: appointment.institution_id }) : null,
                    ])
                    this.exhibition = exhibition
                    this.institution = institution
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
            isOther (term) {
                return String(term).toLowerCase().startsWith('outr')
            },
            mergeCheckins () {
                for (const key of Object.keys(this.form)) {
                    if (this.group[key]) {
                        this.form[key] = this.group[key]
                    }
                }
            },
            newDisability () {
                return {
                    disabilities_type: '',
                    disabilities_other: '',
                    disabilities_count: 1,
                }
            },
            newLanguage () {
                return {
                    languages_name: '',
                    languages_other: '',
                }
            },
        },
    }
</script>
