<template>
    <div class="iande-container">
        <div class="iande-gallery">
            <div class="iande-gallery-item" v-for="item of items" :key="item.id">
                <header class="iande-gallery-item__header">
                    <button type="button" class="iande-button selected" :aria-label="__('Remover', 'iande')" @click="removeItem(item)" v-if="isChecked(item)">
                        <Icon :icon="['fas', 'check-circle']"/>
                    </button>
                    <button type="button" class="iande-button" :aria-label="__('Adicionar', 'iande')" @click="addItem(item)" v-else>
                        <Icon :icon="['fas', 'plus-circle']"/>
                    </button>
                    <span>{{ item.title }}</span>
                </header>
                <img class="iande-gallery-item__thumbnail" :src="thumbnail(item)[0]" :alt="item.thumbnail_alt" :height="thumbnail(item)[2]" :width="thumbnail(item)[1]">
            </div>
        </div>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import { __ } from '../plugins/wp-i18n'
    import { dispatchIandeEvent, onIandeEvent } from '../utils/events'

    export default {
        name: 'IandeViewMode',
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
            thumbnail (item) {
                return item.thumbnail.medium_large
            },
            removeItem (item) {
                dispatchIandeEvent('removeItem', { item })
            },
        },
    }
</script>
