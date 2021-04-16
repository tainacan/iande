<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Avaliação</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="evaluate">
                <template v-if="submitted">
                    <p class="text-center">Avaliação realizada com sucesso!</p>
                </template>

                <template v-else>
                    <div>
                        <label for="type" class="iande-label">Que tipo de visita você realizou? Marque até duas alternativas</label>
                        <CheckboxGroup id="type" columns v-model="form.evaluation_type" :validations="$v.form.evaluation_type" :options="typeOptions"/>
                    </div>

                    <div>
                        <label for="interest" class="iande-label">Qual foi o grau de interesse da maior parte do grupo durante a visita?</label>
                        <RadioGroup id="interest" columns v-model="form.evaluation_interest" :validations="$v.form.evaluation_interest" :options="interestOptions"/>
                    </div>

                    <div>
                        <label for="mood" class="iande-label">Como você classificaria a postura da maior parte do grupo durante a visita? Marque até duas alternativas</label>
                        <CheckboxGroup id="mood" columns v-model="form.evaluation_mood" :validations="$v.form.evaluation_mood" :options="moodOptions"/>
                        <template v-if="hasOther(form.evaluation_mood)">
                            <label for="mood-other" class="iande-label">Qual?</label>
                            <Input id="mood-other" type="text" v-model="form.evaluation_mood_other" :validations="$v.form.evaluation_mood_other"/>
                        </template>
                    </div>

                    <div>
                        <label for="interactive" class="iande-label">A visita educativa suscitou interações entre o visitante e a exposição?</label>
                        <RadioGroup id="interactive" columns v-model="form.evaluation_interactive" :validations="$v.form.evaluation_interactive" :options="interactiveOptions"/>
                    </div>

                    <div>
                        <label for="interaction" class="iande-label">Que tipo de visita você realizou? Marque até duas alternativas</label>
                        <CheckboxGroup id="interaction" columns v-model="form.evaluation_interaction" :validations="$v.form.evaluation_interaction" :options="interactionOptions"/>
                    </div>

                    <div>
                        <label for="difficulty" class="iande-label">Assinale as principais dificuldades encontradas. Marque até duas alternativas</label>
                        <CheckboxGroup id="difficulty" columns v-model="form.evaluation_difficulty" :validations="$v.form.evaluation_difficulty" :options="difficultyOptions"/>
                        <template v-if="hasOther(form.evaluation_difficulty)">
                            <label for="difficulty-other" class="iande-label">Qual?</label>
                            <Input id="difficulty-other" type="text" v-model="form.evaluation_difficulty_other" :validations="$v.form.evaluation_difficulty_other"/>
                        </template>
                    </div>

                    <div>
                        <label for="comment" class="iande-label">Deixe aqui seus comentários<span class="iande-label__optional">(opcional)</span></label>
                        <TextArea id="comment" placeholder="Escreva aqui" v-model="form.evaluation_comment" :validations="$v.form.evaluation_comment"/>
                    </div>

                    <div>
                        <label for="summary" class="iande-label">Resumo da visita</label>
                        <TextArea id="summary" placeholder="Escreva aqui" v-model="form.evaluation_summary" :validations="$v.form.evaluation_summary"/>
                    </div>

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
    import { maxLength, required } from 'vuelidate/lib/validators'

    import CheckboxGroup from '../components/CheckboxGroup.vue'
    import Input from '../components/Input.vue'
    import RadioGroup from '../components/RadioGroup.vue'
    import TextArea from '../components/TextArea.vue'
    import { api, constant, isOther } from '../utils'

    export default {
        name: 'JournalPage',
        components: {
            CheckboxGroup,
            Input,
            RadioGroup,
            TextArea,
        },
        data () {
            return {
                form: {
                    evaluation_comment: '',
                    evaluation_difficulty_other: '',
                    evaluation_difficulty: [],
                    evaluation_interaction: [],
                    evaluation_interactive: null,
                    evaluation_interest: null,
                    evaluation_mood: [],
                    evaluation_mood_other: '',
                    evaluation_summary: '',
                    evaluation_type: [],
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
                evaluation_comment: { },
                evaluation_difficulty: { maxLength: maxLength(2), required },
                evaluation_difficulty_other: { },
                evaluation_interaction: { maxLength: maxLength(2), required },
                evaluation_interactive: { required },
                evaluation_interest: { required },
                evaluation_mood: { maxLength: maxLength(2), required },
                evaluation_mood_other: { },
                evaluation_summary: { required },
                evaluation_type: { maxLength: maxLength(2), required },
            },
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {
                    const group = await api.get('group/get', { ID: Number(qs.get('ID')) })
                    this.group = group
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            async evaluate () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        this.submitted = true
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
            hasOther (arr) {
                return arr.some(item => isOther(item))
            },
        }
    }
</script>
