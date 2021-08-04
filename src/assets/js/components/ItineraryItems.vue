<template>
    <div class="iande-container mt-lg">
        <div class="iande-stack stack-lg">
            <h1>{{ __('Descrição dos itens', 'iande') }}</h1>

            <table>
                <thead>
                    <tr>
                        <th :aria-label="__('Controles', 'iande')"/>
                        <th>{{ __('Miniatura', 'iande') }}</th>
                        <th>{{ __('Título', 'iande') }}</th>
                        <th>{{ __('Descrição', 'iande') }}</th>
                    </tr>
                </thead>
                <Draggable tag="tbody" v-model="itinerary.items" handle=".-handle">
                    <ItineraryItemsRow v-for="item of itinerary.items" :key="item.items_id" :item="item" :meta="itemsCache[item.items_id]" @remove="removeItem"/>
                </Draggable>
            </table>

            <div class="iande-appointment__buttons">
                <a class="iande-button solid" :href="$iandeUrl('itinerary/list')">
                    {{ __('Cancelar', 'iande') }}
                </a>

                <a class="iande-button solid" :href="$iandeUrl(`itinerary/edit/?ID=${itinerary.ID}`)">
                    <Icon icon="plus-circle"/>
                    {{ __('Adicionar itens', 'iande') }}
                </a>

                <button type="button" class="iande-button primary" @click="$emit('update')">
                    {{ __('Salvar alterações', 'iande') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import Draggable from 'vuedraggable'

    import ItineraryItemsRow from '@components/ItineraryItemsRow.vue'

    export default {
        name: 'ItineraryItems',
        components: {
            Draggable,
            ItineraryItemsRow,
        },
        props: {
            formError: { type: String, default: '' },
            itemsCache: { type: Object, required: true },
            itinerary: { type: Object, required: true },
            v: { type: Object, required: true },
        },
        methods: {
            removeItem (id) {
                this.itinerary.items = this.itinerary.items.filter(item => item.items_id != id)
            },
        },
    }
</script>
