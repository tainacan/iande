<template>
    <div class="iande-container">
        <div class="iande-tainacan iande-tainacan-list">
            <table>
                <thead>
                    <tr>
                        <th :aria-label="__('Controles', 'iande')"/>
                        <th>{{ __('Miniatura', 'iande') }}</th>
                        <th>{{ __('Título', 'iande') }}</th>
                        <th>{{ __('Descrição', 'iande') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item of items" :key="item.id">
                        <td class="iande-tainacan-list__controls iande-tainacan-table__controls">
                            <button type="button" class="iande-button iande-tainacan-check-button selected" :aria-label="__('Remover', 'iande')" @click="removeItem(item)" v-if="isChecked(item)">
                                <Icon icon="check-circle"/>
                            </button>
                            <button type="button" class="iande-button iande-tainacan-check-button" :aria-label="__('Adicionar', 'iande')" @click="addItem(item)" v-else>
                                <Icon icon="plus-circle"/>
                            </button>
                        </td>
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
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import { __ } from '@plugins/wp-i18n'
    import { dispatchIandeEvent, onIandeEvent } from '@utils/events'

    export default {
        name: 'IandeListViewMode',
        components: {
            Icon: FontAwesomeIcon,
        },
        props: {
            collectionId: Number,
            displayedMetadata: Array,
            items:  {
                type: Array,
                default: () => [],
            },
            isLoading: false,
            totalItems: Number,
            isFiltersMenuCompressed: Boolean,
            enabledViewModes: Array
        },
        data () {
            return {
                checkedItems: [],
                unsubscribe: null,
            }
        },
        mounted () {
            const iandeEvents = {
                addItem: ({ item }) => {
                    this.checkedItems = [...this.checkedItems, item]
                },
                removeItem: ({ item }) => {
                    this.checkedItems = this.checkedItems.filter(i => i !== item)
                },
                replaceItems: ({ items }) => {
                    this.checkedItems = items
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
            __,
            addItem (item) {
                dispatchIandeEvent('addItem', { item })
            },
            isChecked (item) {
                return this.checkedItems.includes(item)
            },
            removeItem (item) {
                dispatchIandeEvent('removeItem', { item })
            },
            thumbnail (item) {
                return item.thumbnail.thumbnail
            },
        },
    }
</script>
