<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Check-in</h1>
            <form class="iande-form iande-stack stack-lg" @submit.prevent="submit">
                <div>
                    <label for="showed" class="iande-label">O grupo apareceu para a visita?</label>
                    <RadioGroup id="showed" v-model="checkin.showed" :validations="$v.checkin.showed" :options="binaryOptions"/>
                </div>

                <template v-if="showedYes">
                    <div>
                        <label for="hour" class="iande-label">Horário efetivo de início da visita</label>
                        <div class="iande-hint">A visita foi agendada para ocorrer entre <b>{{ group.hour }} - {{ endHour }}</b>. Informe se o grupo iniciou a visita no horário previsto.</div>
                        <RadioGroup id="hour" v-model="checkin.hour" :validations="$v.checkin.hour" :options="binaryOptions"/>
                        <template v-if="checkin.hour === 'no'">
                            <label for="hour-actual" class="iande-hint">Quantas pessoas compareceram efetivamente?</label>
                            <Input id="hour-actual" type="number" v-model.number="checkin.hour_actual" :validations="$v.checkin.hour_actual"/>
                        </template>
                    </div>
                </template>
            </form>
        </div>
    </article>
</template>

<script>
    import { DateTime } from 'luxon'
    import { required, requiredIf } from 'vuelidate/lib/validators'

    import Input from '../components/Input.vue'
    import RadioGroup from '../components/RadioGroup.vue'
    import { api, constant } from '../utils'

    export default {
        name: 'CheckinPage',
        components: {
            Input,
            RadioGroup,
        },
        data () {
            return {
                checkin: {
                    hour: null,
                    hour_actual: null,
                    showed: null,
                },
                formError: '',
                exhibition: null,
                group: null,
            }
        },
        computed: {
            binaryOptions: constant({ 'Não': 'no', 'Sim': 'yes' }),
            endHour () {
                const delta = { minutes: Number(this.exhibition.duration) }
                return DateTime.fromFormat(this.group.hour, 'HH:mm').plus(delta).toFormat('HH:mm')
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.group.exhibition_id)
            },
            showedNo () {
                return this.checkin.showed === 'no'
            },
            showedYes () {
                return this.checkin.showed === 'yes'
            },
        },
        validations () {
            return {
                checkin: {
                    hour: { required: requiredIf(() => this.showedYes) },
                    hour_actual: { required: requiredIf(() => this.checkin.hour === 'no') },
                    showed: { required },
                },
            }
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
            async submit () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {

                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        },
    }
</script>
