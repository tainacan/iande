<template>
    <Modal ref="modal" @close="close">
        <div class="iande-stack">
            <h1>Agendamento enviado com sucesso!</h1>
            <p>Os dados do seu agendamento foram enviados para o museu. Assim que a sua visita for confirmada, você receberá um email com todos os detalhes.</p>
            <button class="iande-button solid" @click="close">
                Voltar aos agendamentos
            </button>
        </div>
    </Modal>
</template>

<script>
    import Modal from './Modal.vue'

    export default {
        name: 'AppointmentSuccessModal',
        components: {
            Modal,
        },
        methods: {
            close () {
                if (this.$refs.modal.isOpen) {
                    this.$refs.modal.close()
                }
                window.location.assign(`${window.IandeSettings.iandeUrl}/appointment/list`)
                window.location.reload()
            },
            open () {
                this.$refs.modal.open()
            },
        }
    }
</script>