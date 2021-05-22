<template>
    <Modal narrow :label="__('Instruções', 'iande')" ref="modal" @close="close">
        <div class="iande-stack stack-lg">
            <h1>{{ __('Agendamento de visita presencial', 'iande') }}</h1>
            <p>{{ __('Agendar uma visita presencial é bem simples e possui essas 3 etapas que apresentamos abaixo:', 'iande') }}</p>
        </div>
        <StepsIndicator/>
        <div class="iande-stack stack-lg">
            <p v-html="__('Na <b>etapa 1</b> solicitamos informações básicas como data, horário e contatos da pessoa ou instituição responsável pela visita. Assim realizamos a reserva da visitação.', 'iande')"/>
            <p v-html="__('Na <b>etapa 2</b> pedimos mais detalhes a respeito do grupo de visitantes, como se há necessidade de algum preparo específico como interprete de outros idiomas ou outras especificidades.', 'iande')"/>
            <p v-html="__('A <b>etapa 3</b> é a confirmação do agendamento, validada pelo próprio Museu a ser visitado. Com a etapa 3 finalizada, seu agendamento estará garantido.', 'iande')"/>
            <button type="button" class="iande-button primary" @click="close">
                {{ __('Iniciar reserva da visita', 'iande') }}
            </button>
        </div>
    </Modal>
</template>

<script>
    import Modal from '@components/Modal.vue'
    import StepsIndicator from '@components/StepsIndicator.vue'

    export default {
        name: 'AppointmentWelcomeModal',
        components: {
            Modal,
            StepsIndicator,
        },
        methods: {
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
