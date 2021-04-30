<template>
    <div class="iande-itinerary-header">
        <div class="iande-itinerary-toolbar">
            <div class="iande-container iande-itinerary-toolbar__row">
                <div>
                    <span>{{ __('Criando roteiro de visita virtual', 'iande') }}</span>
                </div>
                <div>
                    <span>{{ __('Seu roteiro possui', 'iande') }}</span>
                    &nbsp;
                    <div class="iande-itinerary-toolbar__counter" role="button" tabindex="0" @click="toggleItems">
                        {{ items.length }}
                        <Icon icon="caret-down" :class="{ 'fa-flip-vertical': showItems }" v-if="items.length > 0"/>
                    </div>
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
        <div class="iande-itinerary-table" v-if="showItems">
            <div class="iande-container">
                <table>
                    <thead>
                        <tr>
                            <th/>
                            <th>{{ __('Destaque', 'iande') }}</th>
                            <th>{{ __('Título', 'iande') }}</th>
                            <th>{{ __('Descrição', 'iande') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item of items" :key="item.id">
                            <td></td>
                            <td>
                                <img :src="item.thumbnail.thumbnail[0]" :alt="item.thumbnail_alt" height="64" width="64">
                            </td>
                            <td>{{ item.title }}</td>
                            <td>{{ item.description }}</td>
                        </tr>
                    </tbody>
                </table>
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
                showItems: false,
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
        methods: {
            toggleItems () {
                if (this.items.length > 0) {
                    this.showItems = !this.showItems
                }
            },
        }
    }
</script>