<template>
    <Modal ref="modal" narrow @close="close">
        <div class="iande-stack">
            <h1>Cancelar agendamento</h1>
            <p>Tem certeza de que gostaria de cancelar <b class="text-secondary">{{ name }}</b>?</p>
            <div class="iande-appointment__buttons">
                <button class="iande-button solid" @click="close">
                    Voltar
                </button>
                <button class="iande-button primary" @click="cancelAppointment">
                    Confirmar
                </button>
            </div>
        </div>
    </Modal>
</template>

<script>
    import Modal from '@components/Modal.vue'
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
                    return `Agendamento #${this.appointment.ID}`
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
            }
        }
    }
</script>
