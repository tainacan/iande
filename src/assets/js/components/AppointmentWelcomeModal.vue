<template>
    <Modal large label="Instruções" ref="modal" @close="close">
        <article v-once>
            <div class="iande-container narrow iande-stack stack-lg">
                <h1>Agendamento de visita presencial</h1>
                <p>Agendar uma visita presencial é bem simples e possui essas 3 etapas que apresentamos abaixo:</p>
            </div>
            <StepsIndicator/>
            <div class="iande-container narrow iande-stack stack-lg">
                <p>Na <b>etapa 1</b> solicitamos informações básicas como data, horário e contatos da pessoa ou instituição responsável pela visita. Assim realizamos a reserva da visitação.</p>
                <p>Na <b>etapa 2</b> pedimos mais detalhes a respeito do grupo de visitantes, como se há necessidade de algum preparo específico como interprete de outros idiomas ou outras especificidades.</p>
                <p>A <b>etapa 3</b> é a confirmação do agendamento, validada pelo próprio Museu a ser visitado. Com a etapa 3 finalizada, seu agendamento estará garantido.</p>
                <a class="iande-button primary" :href="`${iandeUrl}/appointment/create`" tabindex="0">
                    Iniciar reserva da visita
                </a>
            </div>
        </article>
    </Modal>
</template>

<script>
    import Modal from './Modal.vue'
    import StepsIndicator from './StepsIndicator.vue'
    import { constant } from '../utils'

    export default {
        name: 'AppointmentWelcomeModal',
        components: {
            Modal,
            StepsIndicator,
        },
        computed: {
            iandeUrl: constant(window.IandeSettings.iandeUrl),
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
        }
    }
</script>