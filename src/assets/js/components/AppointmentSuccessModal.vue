<template>
    <Modal ref="modal" :label="__('Sucesso!', 'iande')" narrow @close="close">
        <div class="iande-stack">
            <h1>{{ __('Agendamento enviado com sucesso!', 'iande') }}</h1>
            <p>{{ __('Os dados do seu agendamento foram enviados para o museu. Assim que a sua visita for confirmada, você receberá um email com todos os detalhes.', 'iande') }}</p>
            <button class="iande-button solid" @click="close">
                {{ __('Voltar aos agendamentos', 'iande') }}
            </button>
        </div>
    </Modal>
</template>

<script>
    import Modal from '@components/Modal.vue'

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
                window.location.assign(this.$iandeUrl('appointment/list'))
            },
            open () {
                this.$refs.modal.open()
            },
        }
    }
</script>
