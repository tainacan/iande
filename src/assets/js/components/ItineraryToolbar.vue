<template>
    <div class="iande-itinerary-header">
        <div class="iande-itinerary-toolbar">
            <div class="iande-container iande-itinerary-toolbar__row">
                <div>
                    <span>{{ __('Criando roteiro de visita virtual', 'iande') }}</span>
                </div>
                <div>
                    <span class="hide-sm">{{ __('Seu roteiro possui', 'iande') }}</span>
                    <div class="iande-itinerary-toolbar__counter" role="button" tabindex="0" @click="toggleItems">
                        {{ displayItems.length }}
                        <Icon icon="caret-down" :class="{ 'fa-flip-vertical': showItems }" v-if="displayItems.length > 0"/>
                    </div>
                    <span>{{ _n('item selecionado', 'itens selecionados', displayItems.length, 'iande') }}</span>
                </div>
                <div>
                    <a class="iande-button primary small" :href="$iandeUrl(`itinerary/confirm/?ID=${itinerary.ID}`)">
                        {{ __('Avançar', 'iande') }}
                        <Icon icon="angle-right"/>
                    </a>
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
                    <Draggable tag="tbody" v-model="itinerary.items" handle=".-handle" @end="replaceItems">
                        <ItineraryToolbarRow v-for="item of displayItems" :key="item.id" :item="item"/>
                    </Draggable>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import Draggable from 'vuedraggable'

    import ItineraryToolbarRow from '@components/ItineraryToolbarRow.vue'
    import { api, arrayToMap, qs } from '@utils'
    import { dispatchIandeEvent, onIandeEvent } from '@utils/events'

    export default {
        name: 'ItineraryToolbar',
        components: {
            Draggable,
            ItineraryToolbarRow,
        },
        data () {
            return {
                items: [],
                itinerary: {
                    items: [],
                },
                showItems: false,
                unsubscribe: null,
            }
        },
        computed: {
            displayItems () {
                return this.itinerary.items.map(item => this.itemsIndex[item.items_id])
            },
            itemsIndex () {
                return arrayToMap(this.items, 'id')
            },
        },
        watch: {
            displayItems () {
                if (this.displayItems.length === 0) {
                    this.showItems = false
                }
            },
        },
        async beforeMount () {
            if (qs.has('ID')) {
                try {
                    const [items, itinerary] = await Promise.all([
                        api.get(`${this.$iande.tainacanUrl}/items/?perpage=10000`),
                        api.get('itinerary/get', { ID: Number(qs.get('ID')) }),
                    ])
                    if (!itinerary.items) {
                        itinerary.items = []
                    }
                    this.items = items.items
                    this.itinerary = itinerary
                    this.$nextTick(() => {
                        this.replaceItems()
                    })
                } catch (err) {
                    console.error(err)
                }
            }
        },
        mounted () {
            const iandeEvents = {
                addItem: ({ id }) => {
                    const newItem = { items_id: id, items_description: '' }
                    this.itinerary.items = [...this.itinerary.items, newItem]
                },
                mountedViewMode: () => {
                    this.replaceItems()
                },
                removeItem: ({ id }) => {
                    this.itinerary.items = this.itinerary.items.filter(item => item.items_id != id)
                },
                replaceItems: ({ ids }) => {
                    const items = this.itinerary.items = ids.map(id => {
                        return this.itinerary.items.find(item => item.items_id == id)
                    })
                    this.itinerary.items = items
                },
            }
            this.unsubscribe = onIandeEvent((type, payload) => {
                if (iandeEvents[type]) {
                    iandeEvents[type](payload)
                    if (type !== 'mountedViewMode') {
                        this.updateItinerary()
                    }
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
                dispatchIandeEvent('removeItem', { id: item.id })
            },
            replaceItems () {
                const ids = this.itinerary.items.map(item => item.items_id)
                dispatchIandeEvent('replaceItems', { ids })
            },
            toggleItems () {
                if (this.displayItems.length > 0) {
                    this.showItems = !this.showItems
                }
            },
            updateItinerary () {
                api.post('itinerary/update', this.itinerary)
            },
        },
    }
</script>