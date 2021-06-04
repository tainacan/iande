<template>
    <div class="iande-status-metabox">
        <button type="button" class="button button-primary button-large" @click="publish" v-if="postStatus !== 'publish'">
            {{ __('Confirmar agendamento', 'iande') }}
        </button>
        <div>
            <label for="cancelattion-reason">{{ __('Motivo do cancelamento', 'iande') }}</label>
            <textarea id="cancelattion-reason" v-model="cancellationReason"/>
        </div>
        <button type="button" class="button button-primary button-large button-cancel" @click="cancelPost">
            {{ __('Cancelar agendamento', 'iande') }}
        </button>
        <p class="form-error" v-if="formError">
            {{ __(formError, 'iande') }}
        </p>
    </div>
</template>

<script>
    import { api } from '@utils'

    export default {
        name: 'StatusMetabox.vue',
        props: {
            id: { type: [Number, String], required: true },
            postStatus: { type: String, required: true },
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
                    window.location.reload()
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
                    window.location.reload()
                } catch (err) {
                    this.formError = err
                }
            }
        }
    }
</script>
