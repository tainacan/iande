<template>
    <Modal ref="modal" :label="__('Cancelar', 'iande')" narrow @close="close">
        <div class="iande-stack">
            <h1>{{ __('Cancelar agendamento', 'iande') }}</h1>
            <p>{{ __('Tem certeza de que gostaria de cancelar o agendamento?', 'iande') }}</p>
            <div class="iande-appointment__buttons">
                <button class="iande-button solid" @click="close">
                    {{ __('Voltar', 'iande') }}
                </button>
                <button class="iande-button primary" @click="cancelAppointment">
                    {{ __('Confirmar', 'iande') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script>
    import Modal from '@components/Modal.vue'
    import { __, sprintf } from '@plugins/wp-i18n'
    import { api } from '@utils'

    export default {
        name: 'AppointmentCancelModal',
        components: {
            Modal,
        },
        props: {
            appointment: { type: Object, required: true },
        },
        computed: {
            name () {
                if (this.appointment.name) {
                    return this.appointment.name
                } else {
                    return sprintf(__('Agendamento %s', 'iande'), this.appointment.ID)
                }
            },
        },
        methods: {
            async cancelAppointment () {
                try {
                    await api.post('appointment/cancel', { ID: this.appointment.ID })
                    this.close()
                    window.location.reload()
                } catch (err) {
                    console.error(err)
                }
            },
            close () {
                if (this.$refs.modal.isOpen) {
                    this.$refs.modal.close()
                }
            },
            open () {
                this.$refs.modal.open()
            },
        },
    }
</script>
