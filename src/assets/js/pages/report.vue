<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>{{ __('Avaliação', 'iande') }}</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="sendReport">
                <template v-if="submitted">
                    <p class="text-center">{{ __('Avaliação realizada com sucesso!', 'iande') }}</p>
                    <a class="iande-button primary" :href="$iandeUrl('group/list')">{{ __('Voltar', 'iande') }}</a>
                </template>

                <template v-else>
                    <div>
                        <Label for="type">{{ __('Que tipo de visita você realizou? Marque até duas alternativas', 'iande') }}</Label>
                        <CheckboxGroup id="type" columns v-model="form.report_type" :v="$v.form.report_type" :options="typeOptions"/>
                    </div>

                    <div>
                        <Label for="interest">{{ __('Qual foi o grau de interesse da maior parte do grupo durante a visita?', 'iande') }}</Label>
                        <RadioGroup id="interest" columns v-model="form.report_interest" :v="$v.form.report_interest" :options="interestOptions"/>
                    </div>

                    <div>
                        <Label for="mood">{{ __('Como você classificaria a postura da maior parte do grupo durante a visita? Marque até duas alternativas', 'iande') }}</Label>
                        <CheckboxGroup id="mood" columns v-model="form.report_mood" :v="$v.form.report_mood" :options="moodOptions"/>
                        <template v-if="hasOther(form.report_mood)">
                            <Label for="mood-other">{{ __('Qual?', 'iande') }}</Label>
                            <Input id="mood-other" type="text" v-model="form.report_mood_other" :v="$v.form.report_mood_other"/>
                        </template>
                    </div>

                    <div>
                        <Label for="interactive">{{ __('A visita educativa suscitou interações entre o visitante e a exposição?', 'iande') }}</Label>
                        <RadioGroup id="interactive" columns v-model="form.report_interactive" :v="$v.form.report_interactive" :options="interactiveOptions"/>
                    </div>

                    <div>
                        <Label for="interaction">{{ __('Que tipo de visita você realizou? Marque até duas alternativas', 'iande') }}</Label>
                        <CheckboxGroup id="interaction" columns v-model="form.report_interaction" :v="$v.form.report_interaction" :options="interactionOptions"/>
                    </div>

                    <div>
                        <Label for="difficulty">{{ __('Assinale as principais dificuldades encontradas. Marque até duas alternativas', 'iande') }}</Label>
                        <CheckboxGroup id="difficulty" columns v-model="form.report_difficulty" :v="$v.form.report_difficulty" :options="difficultyOptions"/>
                        <template v-if="hasOther(form.report_difficulty)">
                            <Label for="difficulty-other">{{ __('Qual?', 'iande') }}</Label>
                            <Input id="difficulty-other" type="text" v-model="form.report_difficulty_other" :v="$v.form.report_difficulty_other"/>
                        </template>
                    </div>

                    <div>
                        <Label for="summary" :side="__('(opcional)', 'iande')">{{ __('Resumo da visita', 'iande') }}</Label>
                        <TextArea id="summary" :placeholder="__('Escreva aqui', 'iande')" v-model="form.report_summary" :v="$v.form.report_summary"/>
                    </div>

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
    import { maxLength, required } from 'vuelidate/lib/validators'

    import CheckboxGroup from '@components/CheckboxGroup.vue'
    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import TextArea from '@components/TextArea.vue'
    import { api, constant, isOther, qs } from '@utils'

    export default {
        name: 'EducatorReportPage',
        components: {
            CheckboxGroup,
            Input,
            Label,
            RadioGroup,
            TextArea,
        },
        data () {
            return {
                form: {
                    has_report: 'on',
                    report_difficulty_other: '',
                    report_difficulty: [],
                    report_interaction: [],
                    report_interactive: null,
                    report_interest: null,
                    report_mood: [],
                    report_mood_other: '',
                    report_summary: '',
                    report_type: [],
                },
                formError: '',
                group: null,
                submitted: false,
            }
        },
        computed: {
            difficultyOptions: constant([
                'Atraso do grupo',
                'Comportamento inadequado do grupo',
                'Grupo muito grande',
                'Omissão do responsável',
                'Problemas relacionados à expografia',
                'Museu muito cheio',
                'Nenhum problema',
                'Outros',
            ]),
            interactionOptions: constant([
                'Observação',
                'Leitura',
                'Interação com outros membros do grupo',
                'Interação com o educador/proposta educativa',
                'Interação com os aparatos expositivos/expografia',
                'Interação com o material educativo',
            ]),
            interactiveOptions: constant({
                'Muito': 3,
                'Razoavelmente': 2,
                'Pouco': 1,
                'Nada': 0,
            }),
            interestOptions: constant({
                'Elevado': 3,
                'Mediano': 2,
                'Fraco': 1,
            }),
            moodOptions: constant([
                'Interesse',
                'Apatia',
                'Indisciplina',
                'Tranquilidade',
                'Participação',
                'Outros'
            ]),
            typeOptions: constant([
                'Mais expositiva',
                'Mais dialogada',
                'Mais direcionada',
                'Mais livre',
                'Mais teatral',
                'Mais interrogativa',
            ]),
        },
        validations: {
            form: {
                report_difficulty: { maxLength: maxLength(2), required },
                report_difficulty_other: { },
                report_interaction: { maxLength: maxLength(2), required },
                report_interactive: { required },
                report_interest: { required },
                report_mood: { maxLength: maxLength(2), required },
                report_mood_other: { },
                report_summary: { },
                report_type: { maxLength: maxLength(2), required },
            },
        },
        async beforeMount () {
            if (qs.has('ID')) {
                try {
                    const group = await api.get('group/get', { ID: Number(qs.get('ID')) })
                    this.group = group
                    if (group.has_report === 'on') {
                        this.mergeReports()
                    }
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            hasOther (arr) {
                return arr.some(item => isOther(item))
            },
            mergeReports () {
                for (const key of Object.keys(this.form)) {
                    if (this.group[key]) {
                        this.form[key] = this.group[key]
                    }
                }
            },
            async sendReport () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        await api.post('group/report_update', { ID: this.group.ID, ...this.form })
                        this.submitted = true
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
