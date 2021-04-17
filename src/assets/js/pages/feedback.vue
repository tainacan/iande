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
                        <label for="visit" class="iande-label">O que você achou da visita educativa?</label>
                        <RadioGroup id="visit" columns v-model="form.feedback_visit" :validations="$v.form.feedback_visit" :options="qualityOptions"/>
                    </div>

                    <div>
                        <label for="educator" class="iande-label">O que você achou da atuação do educador?</label>
                        <RadioGroup id="educator" columns v-model="form.feedback_educator" :validations="$v.form.feedback_educator" :options="qualityOptions"/>
                    </div>

                    <div>
                        <label for="mood" class="iande-label">Você acha que a atuação do educador suscitou que tipo de reação do grupo?</label>
                        <RadioGroup id="mood" columns v-model="form.feedback_mood" :validations="$v.form.feedback_mood" :options="moodOptions"/>
                        <template v-if="isOther(form.feedback_mood)">
                            <label for="mood-other" class="iande-label">Qual?</label>
                            <Input id="mood-other" type="text" v-model="form.feedback_mood_other" :validations="$v.form.feedback_mood_other"/>
                        </template>
                    </div>

                    <div>
                        <label for="liked" class="iande-label">O que você mais gostou na visita?</label>
                        <RadioGroup id="liked" columns v-model="form.feedback_liked" :validations="$v.form.feedback_liked" :options="likedOptions"/>
                        <template v-if="isOther(form.feedback_liked)">
                            <label for="liked-other" class="iande-label">Qual?</label>
                            <Input id="liked-other" type="text" v-model="form.feedback_liked_other" :validations="$v.form.feedback_liked_other"/>
                        </template>
                    </div>

                    <div>
                        <label for="disliked" class="iande-label">O que você mais gostou na visita?</label>
                        <RadioGroup id="disliked" columns v-model="form.feedback_disliked" :validations="$v.form.feedback_disliked" :options="dislikedOptions"/>
                        <template v-if="isOther(form.feedback_disliked)">
                            <label for="disliked-other" class="iande-label">Qual?</label>
                            <Input id="disliked-other" type="text" v-model="form.feedback_disliked_other" :validations="$v.form.feedback_disliked_other"/>
                        </template>
                    </div>

                    <div>
                        <label for="comment" class="iande-label">Deixe aqui seus comentários</label>
                        <TextArea id="comment" placeholder="Escreva aqui" v-model="form.feedback_comment" :validations="$v.form.feedback_comment"/>
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
    import { required } from 'vuelidate/lib/validators'

    import Input from '../components/Input.vue'
    import RadioGroup from '../components/RadioGroup.vue'
    import TextArea from '../components/TextArea.vue'
    import { api, constant, isOther } from '../utils'

    export default {
        name: 'FeedbackPage',
        components: {
            Input,
            RadioGroup,
            TextArea,
        },
        data () {
            return {
                form: {
                    feedback_comment: '',
                    feedback_disliked: null,
                    feedback_disliked_other: '',
                    feedback_educator: null,
                    feedback_liked: null,
                    feedback_liked_other: '',
                    feedback_mood: null,
                    feedback_mood_other: '',
                    feedback_visit: null,
                },
                formError: '',
                group: null,
                submitted: false,
            }
        },
        computed: {
            dislikedOptions: constant([
                'Do acervo exposto',
                'Dos textos da exposição',
                'Da atuação do educador/visita educativa',
                'Do comportamento dos alunos',
                'Dos materiais educativos',
                'Outros'
            ]),
            likedOptions: constant([
                'Observar o acervo',
                'Interagir com a exposição',
                'Ler os textos da exposição',
                'Da atuação do educador/visita educativa',
                'Dos materiais educativos',
                'Outros'
            ]),
            moodOptions: constant([
                'Interesse',
                'Apatia',
                'Indisciplina',
                'Tranquilidade',
                'Participação',
                'Outros'
            ]),
            qualityOptions: constant({
                'Muito satisfatória': 4,
                'Satisfatória': 3,
                'Pouco satisfatória': 2,
                'Insatisfatória': 1
            }),
        },
        validations: {
            form: {
                feedback_comment: { },
                feedback_disliked: { required },
                feedback_disliked_other: { },
                feedback_educator: { required },
                feedback_liked: { required },
                feedback_liked_other: { },
                feedback_mood: { required },
                feedback_mood_other: { },
                feedback_visit: { required },
            }
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
            isOther,
        }
    }
</script>
