<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>{{ __('Avaliação', 'iande') }}</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="sendFeedback">
                <template v-if="submitted">
                    <p class="text-center">{{ __('Avaliação realizada com sucesso!', 'iande') }}</p>
                    <a class="iande-button primary" :href="$iandeUrl('appointment/list')">{{ __('Voltar', 'iande') }}</a>
                </template>

                <template v-else>
                    <div>
                        <Label for="visit">{{ __('O que você achou da visita educativa?', 'iande') }}</Label>
                        <RadioGroup id="visit" columns v-model="form.feedback_visit" :v="$v.form.feedback_visit" :options="qualityOptions"/>
                    </div>

                    <div>
                        <Label for="educator">{{ __('O que você achou da atuação do educador?', 'iande') }}</Label>
                        <RadioGroup id="educator" columns v-model="form.feedback_educator" :v="$v.form.feedback_educator" :options="qualityOptions"/>
                    </div>

                    <div>
                        <Label for="mood">{{ __('Você acha que a atuação do educador suscitou que tipo de reação do grupo?', 'iande') }}</Label>
                        <RadioGroup id="mood" columns v-model="form.feedback_mood" :v="$v.form.feedback_mood" :options="moodOptions"/>
                        <template v-if="isOther(form.feedback_mood)">
                            <Label for="mood-other">{{ __('Qual?', 'iande') }}</Label>
                            <Input id="mood-other" type="text" v-model="form.feedback_mood_other" :v="$v.form.feedback_mood_other"/>
                        </template>
                    </div>

                    <div>
                        <Label for="liked">{{ __('O que você mais gostou na visita?', 'iande') }}</Label>
                        <RadioGroup id="liked" columns v-model="form.feedback_liked" :v="$v.form.feedback_liked" :options="likedOptions"/>
                        <template v-if="isOther(form.feedback_liked)">
                            <Label for="liked-other">{{ __('Qual?', 'iande') }}</Label>
                            <Input id="liked-other" type="text" v-model="form.feedback_liked_other" :v="$v.form.feedback_liked_other"/>
                        </template>
                    </div>

                    <div>
                        <Label for="disliked">{{ __('O que você menos gostou na visita?', 'iande') }}</Label>
                        <RadioGroup id="disliked" columns v-model="form.feedback_disliked" :v="$v.form.feedback_disliked" :options="dislikedOptions"/>
                        <template v-if="isOther(form.feedback_disliked)">
                            <Label for="disliked-other">{{ __('Qual?', 'iande') }}</Label>
                            <Input id="disliked-other" type="text" v-model="form.feedback_disliked_other" :v="$v.form.feedback_disliked_other"/>
                        </template>
                    </div>

                    <div>
                        <Label for="comment">{{ __('Deixe aqui seus comentários', 'iande') }}</Label>
                        <TextArea id="comment" :placeholder="__('Escreva aqui', 'iande')" v-model="form.feedback_comment" :v="$v.form.feedback_comment"/>
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
    import { required } from 'vuelidate/lib/validators'

    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import TextArea from '@components/TextArea.vue'
    import { __, _x } from '@plugins/wp-i18n'
    import { api, constant, isOther, qs } from '@utils'

    export default {
        name: 'FeedbackPage',
        components: {
            Input,
            Label,
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
                    has_feedback: 'on',
                },
                formError: '',
                group: null,
                submitted: false,
            }
        },
        computed: {
            dislikedOptions: constant([
                __('Do acervo exposto', 'iande'),
                __('Dos textos da exposição', 'iande'),
                __('Da atuação do educador/visita educativa', 'iande'),
                __('Do comportamento dos estudantes', 'iande'),
                __('Dos materiais educativos', 'iande'),
                _x('Outros', 'feedback', 'iande'),
            ]),
            likedOptions: constant([
                __('Observar o acervo', 'iande'),
                __('Interagir com a exposição', 'iande'),
                __('Ler os textos da exposição', 'iande'),
                __('Da atuação do educador/visita educativa', 'iande'),
                __('Dos materiais educativos', 'iande'),
                _x('Outros', 'feedback', 'iande'),
            ]),
            moodOptions: constant([
                __('Interesse', 'iande'),
                __('Apatia', 'iande'),
                __('Indisciplina', 'iande'),
                __('Tranquilidade', 'iande'),
                __('Participação', 'iande'),
                _x('Outros', 'feedback', 'iande'),
            ]),
            qualityOptions: constant({
                [__('Muito satisfatória', 'iande')]: 4,
                [__('Satisfatória', 'iande')]: 3,
                [__('Pouco satisfatória', 'iande')]: 2,
                [__('Insatisfatória', 'iande')]: 1
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
            if (qs.has('ID')) {
                try {
                    const group = await api.get('group/get', { ID: Number(qs.get('ID')) })
                    this.group = group
                    if (group.has_feedback === 'on') {
                        this.mergeFeedbacks()
                    }
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            isOther,
            mergeFeedbacks () {
                for (const key of Object.keys(this.form)) {
                    if (this.group[key]) {
                        this.form[key] = this.group[key]
                    }
                }
            },
            async sendFeedback () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        await api.post('group/feedback_update', { ID: this.group.ID, ...this.form })
                        this.submitted = true
                    } catch (err) {
                        this.formError = err
                    }
                }
            },
        }
    }
</script>
