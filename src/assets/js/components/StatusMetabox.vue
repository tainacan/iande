<template>
    <div class="iande-status-metabox">
        <button type="button" class="button button-primary button-large" @click="publish">
            Confirmar agendamento
        </button>
        <div>
            <label for="cancelattion-reason">Motivo do cancelamento</label>
            <textarea id="cancelattion-reason" v-model="cancellationReason"/>
        </div>
        <button type="button" class="button button-primary button-large button-cancel" @click="cancelPost">
            Cancelar agendamento
        </button>
        <p class="form-error" v-if="formError">
            {{ formError }}
        </p>
    </div>
</template>

<script>
    import { api } from '../utils'

    export default {
        name: 'StatusMetabox.vue',
        props: {
            id: { type: [Number, String], required: true },
        },
        data () {
            return {
                cancellationReason: '',
                formError: '',
            }
        },
        methods: {
            async cancelPost () {
                this.formError = ''
                try {
                    await api.post('appointment/cancel', {
                        ID: this.id,
                        reason: this.cancellationReason,
                    })
                } catch (err) {
                    this.formError = err
                }
            },
            async publish () {
                this.formError = ''
                try {
                    await api.post('appointment/set_status', {
                        ID: this.id,
                        post_status: 'publish',
                    })
                } catch (err) {
                    this.formError = err
                }
            }
        }
    }
</script>