<template>
    <div class="iande-container">
        <div class="iande-tainacan iande-tainacan-masonry" ref="masonry">
            <div class="iande-tainacan-masonry-item" :class="size" v-for="item of items" :key="item.id">
                <header class="iande-tainacan-masonry-item__header">
                    <button type="button" class="iande-button iande-tainacan-check-button selected" :aria-label="__('Remover', 'iande')" @click="removeItem(item)" v-if="isChecked(item)">
                        <Icon icon="circle-check"/>
                    </button>
                    <button type="button" class="iande-button iande-tainacan-check-button" :aria-label="__('Adicionar', 'iande')" @click="addItem(item)" v-else>
                        <Icon icon="circle-plus"/>
                    </button>
                    <span v-html="getMeta(item, 'title')"/>
                </header>
                <img class="iande-tainacan-masonry-item__thumbnail" :src="thumbnail(item)[0]" :alt="item.thumbnail_alt" :height="thumbnail(item)[2]" :width="thumbnail(item)[1]" @click="seeDetails(item)">
            </div>
        </div>
        <ViewModeModal ref="modal" :item="selectedItem" v-if="selectedItem"/>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import Masonry from 'masonry-layout'

    import ViewModeModal from '@components/ViewModeModal.vue'
    import ViewMode from '@mixins/ViewMode'

    export default {
        name: 'IandeMasonryViewMode',
        components: {
            Icon: FontAwesomeIcon,
            ViewModeModal,
        },
        mixins: [ViewMode],
        data () {
            return {
                masonry: null,
                size: 'cols-4',
            }
        },
        watch: {
            items () {
                this.reloadMasonry()
            },
        },
        mounted () {
            if (window.ResizeObserver) {
                new ResizeObserver(this.onResize).observe(document.querySelector('.items-list-area'))
            }
        },
        methods: {
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
            thumbnail (item) {
                return item.thumbnail.medium_large
            },
        },
    }
</script>
