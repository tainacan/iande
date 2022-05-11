<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Check-in</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="checkin">
                <template v-if="submitted">
                    <p class="text-center">{{ __('Check-in realizado com sucesso!', 'iande') }}</p>
                    <a class="iande-button primary" :href="$iandeUrl('group/list')">{{ __('Voltar', 'iande') }}</a>
                </template>

                <template v-else>
                    <div>
                        <Label for="showed">{{ __('O grupo apareceu para a visita?', 'iande') }}</Label>
                        <RadioGroup id="showed" v-model="form.checkin_showed" :v="$v.form.checkin_showed" :options="binaryOptions"/>
                    </div>

                    <template v-if="showedYes">
                        <div>
                            <Label for="hour">{{ __('Horário efetivo de início da visita', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('A visita foi agendada para ocorrer entre <b>%s - %s</b>. Informe se o grupo iniciou a visita no horário previsto.', 'iande'), group.hour, endHour)"/>
                            <RadioGroup id="hour" v-model="form.checkin_hour" :v="$v.form.checkin_hour" :options="binaryOptions"/>
                        </div>

                        <div>
                            <Label for="num-people">{{ __('Quantidade efetiva de integrantes do grupo', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('O agendamendo considera a previsão de <b>%s pessoas</b> no total. Infome se o grupo presente condiz com informações agendadas.', 'iande'), group.num_people)"/>
                            <RadioGroup id="num-people" v-model="form.checkin_num_people" :v="$v.form.checkin_num_people" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_num_people === 'no'">
                            <label for="num-people-actual" class="iande-hint">{{ __('Quantas pessoas compareceram efetivamente?', 'iande') }}</label>
                            <Input id="num-people-actual" type="number" v-model.number="form.checkin_num_people_actual" :v="$v.form.checkin_num_people_actual"/>
                        </div>

                        <div>
                            <Label for="num-responsible">{{ __('Quantidade efetiva de responsáveis', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('O agendamendo considera a previsão de <b>%s responsáveis</b>. Infome se o grupo presente condiz com informações agendadas', 'iande'), group.num_responsible)"/>
                            <RadioGroup id="num-responsible" v-model="form.checkin_num_responsible" :v="$v.form.checkin_num_responsible" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_num_responsible === 'no'">
                            <label for="num-responsible-actual" class="iande-hint">{{ __('Quantos responsáveis compareceram efetivamente?', 'iande') }}</label>
                            <Input id="num-responsible-actual" type="number" v-model.number="form.checkin_num_responsible_actual" :v="$v.form.checkin_num_responsible_actual"/>
                        </div>

                        <div>
                            <Label for="disabilities">{{ __('Quantidade efetiva de pessoas com cada tipo de deficiência', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('O agendamendo prevê %s. Infome se o grupo presente condiz com informações do agendamento.', 'iande'), disabilities)"/>
                            <RadioGroup id="disabilities" v-model="form.checkin_disabilities" :v="$v.form.checkin_disabilities" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_disabilities === 'no'">
                            <label for="disabilities-actual" class="iande-hint">{{ __('Quantas pessoas com deficiência apareceram efetivamente?', 'iande') }}</label>
                            <div class="iande-complex-field">
                                <Repeater id="disabilities-actual" v-model="form.checkin_disabilities_actual" :factory="newDisability" :v="$v.form.checkin_disabilities_actual">
                                    <template #item="{ id, onUpdate, v, value }">
                                        <div :key="id">
                                            <DisabilityInfo :id="id" :value="value" :v="v" @updateValue="onUpdate"/>
                                        </div>
                                    </template>
                                    <template #addItem="{ action }">
                                        <div class="iande-add-item" role="button" tabindex="0" @click="action">
                                            <span><Icon icon="circle-plus"/></span>
                                            <div class="iande-label">{{ __('Adicionar deficiência', 'iande') }}</div>
                                        </div>
                                    </template>
                                </Repeater>
                            </div>
                        </div>

                        <div>
                            <Label for="languages">{{ __('Quantidade efetiva de pessoas falando outros idiomas diferentes de português', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('O agendamendo prevê %s. Infome se o grupo presente condiz com informações do agendamento.', 'iande'), languages)"/>
                            <RadioGroup id="languages" v-model="form.checkin_languages" :v="$v.form.checkin_languages" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_languages === 'no'">
                            <label for="languages-actual" class="iande-hint">{{ __('Quais os idiomas falados pelo grupo efetivamente?', 'iande') }}</label>
                            <div class="iande-complex-field">
                                <Repeater id="languages-actual" v-model="form.checkin_languages_actual" :factory="newLanguage" :v="$v.form.checkin_languages_actual">
                                    <template #item="{ id, onUpdate, v, value }">
                                        <div :key="id">
                                            <LanguageInfo :id="id" :value="value" :v="v" @updateValue="onUpdate"/>
                                        </div>
                                    </template>
                                    <template #addItem="{ action }">
                                        <div class="iande-add-item" role="button" tabindex="0" @click="action">
                                            <span><Icon icon="circle-plus"/></span>
                                            <div class="iande-label">{{ __('Adicionar idioma', 'iande') }}</div>
                                        </div>
                                    </template>
                                </Repeater>
                            </div>
                        </div>

                        <div>
                            <Label for="scholarity">{{ __('Confirmação de escolaridade', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('O agendamendo prevê <b>%s</b>. Infome se o grupo presente condiz com informações do agendamento.', 'iande'), group.scholarity)"/>
                            <RadioGroup id="scholarity" v-model="form.checkin_scholarity" :v="$v.form.checkin_scholarity" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_scholarity === 'no'">
                            <label for="scholarity-actual" class="iande-hint">{{ __('Qual a escolaridade do grupo efetivamente?', 'iande') }}</label>
                            <Select id="scholarity-actual" v-model="form.checkin_scholarity_actual" :v="$v.form.checkin_scholarity_actual" :options="$iande.scholarity"/>
                        </div>

                        <div>
                            <Label for="age-range">{{ __('Confirmação de faixa etária', 'iande') }}</Label>
                            <div class="iande-hint" v-html="sprintf(__('O agendamendo prevê <b>%s</b>. Infome se o grupo presente condiz com informações do agendamento.', 'iande'), group.age_range.toLocaleLowerCase())"/>
                            <RadioGroup id="age-range" v-model="form.checkin_age_range" :v="$v.form.checkin_age_range" :options="binaryOptions"/>
                        </div>
                        <div v-if="form.checkin_age_range === 'no'">
                            <label for="age-actual-actual" class="iande-hint">{{ __('Qual a faixa etária efetivamente?', 'iande') }}</label>
                            <Select id="age-range-actual" v-model="form.checkin_age_range_actual" :v="$v.form.checkin_age_range_actual" :options="$iande.ageRanges"/>
                        </div>

                        <div>
                            <Label for="institutional">{{ __('O grupo é institucional?', 'iande') }}</Label>
                            <RadioGroup id="institutional" v-model="form.checkin_institutional" :v="$v.form.checkin_institutional" :options="binaryOptions"/>
                        </div>

                        <div v-if="form.checkin_institutional === 'yes'">
                            <Label for="institution">{{ __('Tipo / perfil da instituição', 'iande') }}</Label>
                            <template v-if="institution && appointment.group_nature === 'institutional'">
                                <div class="iande-hint" v-html="sprintf(__('O agendamendo prevê <b>%s</b>. Infome se o grupo presente condiz com informações do agendamento.', 'iande'), institution.profile)"/>
                                <RadioGroup id="institution" v-model="form.checkin_institution" :v="$v.form.checkin_institution" :options="binaryOptions"/>
                            </template>
                            <template v-else>
                                <label for="institution-actual" class="iande-hint">{{ __('Qual o perfil da instituição?', 'iande') }}</label>
                                <Select id="institution-actual" v-model="form.checkin_institution_actual" :v="$v.form.checkin_institution_actual" :options="$iande.profiles"/>
                            </template>
                        </div>
                        <div v-if="form.checkin_institution === 'no'">
                            <label for="institution-actual" class="iande-hint">{{ __('Qual o perfil da instituição efetivamente?', 'iande') }}</label>
                            <Select id="institution-actual" v-model="form.checkin_institution_actual" :v="$v.form.checkin_institution_actual" :options="$iande.profiles"/>
                        </div>
                    </template>

                    <template v-else-if="showedNo">
                        <div>
                            <Label for="noshow-type">{{ __('A visita não foi realizada devido a', 'iande') }}</Label>
                            <RadioGroup id="noshow-type" v-model="form.checkin_noshow_type" :v="$v.form.checkin_noshow_type" :options="noshowTypeOptions"/>
                        </div>

                        <div v-if="form.checkin_noshow_type">
                            <Label for="noshow-reason">{{ __('Qual desafio impossibilitou a visita?', 'iande') }}</Label>
                            <RadioGroup id="noshow-reason" columns v-model="form.checkin_noshow_reason" :v="$v.form.checkin_noshow_reason" :options="noshowReasonOptions"/>
                            <template v-if="isOther(form.checkin_noshow_reason)">
                                <Label for="noshow-reason-other">{{ __('Qual?', 'iande') }}</Label>
                                <TextArea id="noshow-reason-other" columns v-model="form.checkin_noshow_reason_other" :v="$v.form.checkin_noshow_reason_other"/>
                            </template>
                        </div>
                    </template>

                    <div class="iande-stack stack-md">
                        <div class="iande-form-error" v-if="formError">
                            <span>{{ __(formError, 'iande') }}</span>
                        </div>
                        <button class="iande-button primary" type="submit">
                            {{ __('Enviar', 'iande') }}
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

    import DisabilityInfo from '@components/DisabilityInfo.vue'
    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import LanguageInfo from '@components/LanguageInfo.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import Repeater from '@components/Repeater.vue'
    import Select from '@components/Select.vue'
    import TextArea from '@components/TextArea.vue'
    import { __, _n, _x } from '@plugins/wp-i18n'
    import { api, constant, isOther, joinMany, qs } from '@utils'

    export default {
        name: 'CheckinPage',
        components: {
            DisabilityInfo,
            Input,
            Label,
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
            binaryOptions: constant({
                [__('Não', 'iande')]: 'no',
                [__('Sim', 'iande')]: 'yes',
            }),
            disabilities () {
                const disabilities = this.group.disabilities
                if (!disabilities || disabilities.length === 0) {
                    return __('<b>nenhuma</b> pessoa com deficiência', 'iande')
                } else {
                    return joinMany(disabilities.map(disability => {
                        let dis = '<b>'
                        if (isOther(disability.disabilities_type) && disability.disabilities_other) {
                            dis += disability.disabilities_other
                        } else {
                            dis += disability.disabilities_type
                        }
                        dis += `</b> (<b>${disability.disabilities_count}</b> ${_n('pessoa', 'pessoas', disability.disabilities_count, 'iande')})`
                        return dis
                    }))
                }
            },
            endHour () {
                const delta = { minutes: Number(this.exhibition?.duration ?? 30) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat(__('HH:mm', 'iande'))
            },
            languages () {
                const languages = this.group.languages
                if (!languages || languages.length === 0) {
                    return _x('<b>apenas português</b>', 'main language', 'iande')
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
                        __('Exposição fechada por problemas de infraestrutura ou queda de energia', 'iande'),
                        __('Não havia educador disponível para realizar a visita', 'iande'),
                        __('A exposição estava com excesso de grupos', 'iande'),
                    ]
                } else {
                    return [
                        __('Problemas de deslocamento até a exposição/museu (trânsito, endereço errado, atraso do ônibus, atraso de responsáveis)', 'iande'),
                        __('O grupo preferiu visitar a exposição sem a presença do educador', 'iande'),
                        __('O grupo optou por realizar outra atividade na instituição', 'iande'),
                        __('A visita foi cancelada no mesmo dia', 'iande'),
                        __('Não sei', 'iande'),
                        _x('Outro', 'motivo', 'iande'),
                    ]
                }
            },
            noshowTypeOptions: constant({
                [__('Problemas internos', 'iande')]: 'internal',
                [__('Problemas da instituição visitante', 'iande')]: 'visitor',
            }),
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
                    checkin_age_range_actual: { },
                    checkin_disabilities: { required: requiredIf(() => this.showedYes) },
                    checkin_disabilities_actual: {
                        $each: {
                            disabilities_count: { integer, required },
                            disabilities_other: { },
                            disabilities_type: { required },
                        },
                    },
                    checkin_hour: { required: requiredIf(() => this.showedYes) },
                    checkin_institution: { required: requiredIf(() => this.form.checkin_institutional === 'yes') },
                    checkin_institution_actual: { },
                    checkin_institutional: { required: requiredIf(() => this.showedYes) },
                    checkin_languages: { required: requiredIf(() => this.showedYes) },
                    checkin_languages_actual: {
                        $each: {
                            languages_name: { required },
                            languages_other: { },
                        },
                    },
                    checkin_noshow_reason: { required: requiredIf(() => this.showedNo) },
                    checkin_noshow_reason_other: { },
                    checkin_noshow_type: { required: requiredIf(() => this.showedNo) },
                    checkin_num_people: { required: requiredIf(() => this.showedYes) },
                    checkin_num_people_actual: { required: requiredIf(() => this.form.checkin_num_people === 'no'), numeric },
                    checkin_num_responsible: { required: requiredIf(() => this.showedYes) },
                    checkin_num_responsible_actual: { required: requiredIf(() => this.form.checkin_num_responsible === 'no'), numeric },
                    checkin_scholarity: { required: requiredIf(() => this.showedYes) },
                    checkin_scholarity_actual: { },
                    checkin_showed: { required },
                },
            }
        },
        async beforeMount () {
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
            isOther,
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
