<template>
    <div class="iande-itinerary-header">
        <div class="iande-itinerary-toolbar">
            <div class="iande-container iande-itinerary-toolbar__row">
                <div>
                    <span>{{ __('Criando roteiro de visita virtual', 'iande') }}</span>
                </div>
                <div>
                    <span>{{ __('Seu roteiro possui', 'iande') }}</span>
                    <div class="iande-itinerary-toolbar__counter" role="button" tabindex="0" @click="toggleItems">
                        {{ items.length }}
                        <Icon icon="caret-down" :class="{ 'fa-flip-vertical': showItems }" v-if="items.length > 0"/>
                    </div>
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
                            <th :aria-label="__('Controles', 'iande')"/>
                            <th>{{ __('Miniatura', 'iande') }}</th>
                            <th>{{ __('Título', 'iande') }}</th>
                            <th>{{ __('Descrição', 'iande') }}</th>
                        </tr>
                    </thead>
                    <Draggable tag="tbody" v-model="items" handle=".-handle" @end="replaceItems">
                        <tr v-for="item of items" :key="item.id">
                            <td class="iande-itinerary-table__controls iande-tainacan-table__controls">
                                <div role="button" tabindex="0" :aria-role="__('Remover', 'iande')" @click="removeItem(item)">
                                    <Icon :icon="['far', 'trash-alt']"/>
                                </div>
                                <div class="-handle" aria-hidden="true">
                                    <Icon icon="grip-vertical"/>
                                </div>
                            </td>
                            <td>
                                <img :src="item.thumbnail.thumbnail[0]" :alt="item.thumbnail_alt" height="64" width="64">
                            </td>
                            <td>{{ item.title }}</td>
                            <td>{{ item.description }}</td>
                        </tr>
                    </Draggable>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import Draggable from 'vuedraggable'

    import { dispatchIandeEvent, onIandeEvent } from '@utils/events'

    export default {
        name: 'ItineraryToolbar',
        components: {
            Draggable,
        },
        data () {
            return {
                showItems: false,
                items: [],
                unsubscribe: null,
            }
        },
        watch: {
            items () {
                if (this.items.length === 0) {
                    this.showItems = false
                }
            }
        },
        mounted () {
            const iandeEvents = {
                addItem: ({ item }) => {
                    this.items = [...this.items, item]
                },
                removeItem: ({ item }) => {
                    this.items = this.items.filter(i => i !== item)
                },
                replaceItems: ({ items }) => {
                    this.items = items
                },
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
            removeItem (item) {
                dispatchIandeEvent('removeItem', { item })
            },
            replaceItems () {
                dispatchIandeEvent('replaceItems', { items: this.items })
            },
            toggleItems () {
                if (this.items.length > 0) {
                    this.showItems = !this.showItems
                }
            },
        }
    }
</script>