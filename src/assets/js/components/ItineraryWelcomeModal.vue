<template>
    <Modal id="iande-itinerary-welcome-modal" narrow :label="__('Instruções', 'iande')" ref="modal" @close="close">
        <div class="iande-stack stack-md" v-if="screen === 1">
            <h1>{{ __('Boas vindas ao Criador de Roteiros', 'iande') }}</h1>
            <p>{{ __('Você está prestes a iniciar a seleção dos itens que vão compor seu roteiro virtual.', 'iande') }}</p>
            <p>
                {{ __('Para selecionar um item, clique no botão', 'iande') }}
                <button type="button" class="iande-button iande-tainacan-check-button" :aria-label="__('Adicionar', 'iande')" disabled>
                    <Icon icon="plus-circle"/>
                </button>.
            </p>
            <p>
                {{ __('Quando selecionado, o item mostrará o indicador', 'iande') }}
                <button type="button" class="iande-button iande-tainacan-check-button selected" :aria-label="__('Remover', 'iande')" disabled>
                    <Icon icon="check-circle"/>
                </button>
                {{ __('e, para desselecionar, clique no botão novamente.', 'iande') }}
            </p>
            <p class="page">1/2</p>
            <button type="button" class="iande-button outline" @click="advance">
                {{ __('Próximo', 'iande') }}
            </button>
        </div>
        <div class="iande-stack stack-md" v-else>
            <h1>{{ __('Acompanhe a criação pelo menu superior', 'iande') }}</h1>
            <p>{{ __('Acompanhe a quantidade de itens selecionados pela barra na parte superior da tela:', 'iande') }}</p>
            <p>{{ __('Clique no número para visualizar os itens selecionados.', 'iande') }}</p>
            <p>
                {{ __('Finalizando a seleção, clique em', 'iande') }}
                <button type="button" class="iande-button primary small" disabled>
                    {{ __('Avançar', 'iande') }}
                    <Icon icon="angle-right"/>
                </button>
                {{ __('para prosseguir.', 'iande') }}
            </p>
            <p>{{ __('Você pode editar seu roteiro a qualquer momento.', 'iande') }}</p>
            <p class="page">2/2</p>
            <button type="button" class="iande-button outline" @click="close">
                {{ __('Iniciar seleção', 'iande') }}
            </button>
        </div>
    </Modal>
</template>

<script>
    import Modal from './Modal.vue'

    export default {
        name: 'ItineraryWelcomeModal',
        components: {
            Modal,
        },
        data () {
            return {
                screen: 1,
            }
        },
        methods: {
            advance () {
                this.screen = 2
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
