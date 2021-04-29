<template>
    <div class="iande-itinerary-toolbar">
        <div class="iande-container iande-itinerary-toolbar__row">
            <div>
                <span>{{ __('Criando roteiro de visita virtual', 'iande') }}</span>
            </div>
            <div>
                <span>{{ __('Seu roteiro possui', 'iande') }}</span>
                &nbsp;
                <div class="iande-itinerary-toolbar__counter">{{ items.length }}</div>
                &nbsp;
                <span>{{ _n('item selecionado', 'itens selecionados', items.length, 'iande') }}</span>
            </div>
            <div>
                <button type="button" class="iande-button primary small">
                    {{ __('Avançar', 'iande') }}
                    <Icon icon="angle-right"/>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { onIandeEvent } from '../utils/events'

    export default {
        name: 'ItineraryToolbar',
        data () {
            return {
                items: [],
                unsubscribe: null,
            }
        },
        mounted () {
            const iandeEvents = {
                addItem: ({ item }) => {
                    this.items = [...this.items, item]
                },
                removeItem: ({ item }) => {
                    this.items = this.items.filter(x => x !== item)
                }
            }
            this.unsubscribe = onIandeEvent((type, payload) => {
                if (iandeEvents[type]) {
                    iandeEvents[type](payload)
                }
            })
        },
        beforeDestroy () {
            if (this.unsubscribe) {
                this.unsubscribe()
            }
        },
    }
</script>