<template>
    <div class="iande-container">
        <div class="iande-tainacan iande-tainacan-masonry" ref="masonry">
            <div class="iande-tainacan-masonry-item" :class="size" v-for="item of items" :key="item.id">
                <header class="iande-tainacan-masonry-item__header">
                    <button type="button" class="iande-button iande-tainacan-check-button selected" :aria-label="__('Remover', 'iande')" @click="removeItem(item)" v-if="isChecked(item)">
                        <Icon icon="check-circle"/>
                    </button>
                    <button type="button" class="iande-button iande-tainacan-check-button" :aria-label="__('Adicionar', 'iande')" @click="addItem(item)" v-else>
                        <Icon icon="plus-circle"/>
                    </button>
                    <span>{{ item.title }}</span>
                </header>
                <img class="iande-tainacan-masonry-item__thumbnail" :src="thumbnail(item)[0]" :alt="item.thumbnail_alt" :height="thumbnail(item)[2]" :width="thumbnail(item)[1]">
            </div>
        </div>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import Masonry from 'masonry-layout'

    import { __ } from '@plugins/wp-i18n'
    import { dispatchIandeEvent, onIandeEvent } from '@utils/events'

    export default {
        name: 'IandeMasonryViewMode',
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
                masonry: null,
                size: 'cols-4',
                unsubscribe: null,
            }
        },
        watch: {
            items () {
                this.reloadMasonry()
            }
        },
        mounted () {
            if (window.ResizeObserver) {
                new ResizeObserver(this.onResize).observe(document.querySelector('.items-list-area'))
            }

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
            onResize (entries) {
                const lastEntry = entries[0]
                const width = lastEntry.contentRect.width
                if (width > 1200) {
                    this.size = 'cols-4'
                } else if (width > 900) {
                    this.size = 'cols-3'
                } else if (width > 600) {
                    this.size = 'cols-2'
                } else {
                    this.size = 'cols-1'
                }
                this.reloadMasonry()
            },
            reloadMasonry () {
                this.$nextTick(() => {
                    if (!this.masonry) {
                        this.masonry = new Masonry(this.$refs.masonry, {
                            itemSelector: ".iande-tainacan-masonry-item",
                        })
                    } else {
                        this.masonry.reloadItems()
                        this.masonry.layout()
                    }
                })
            },
            removeItem (item) {
                dispatchIandeEvent('removeItem', { item })
            },
            thumbnail (item) {
                return item.thumbnail.medium_large
            },
        },
    }
</script>
