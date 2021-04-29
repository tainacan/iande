<template>
    <div class="iande-container">
        <div class="iande-gallery">
            <div class="iande-gallery-item" v-for="item in items" :key="item.id">
                <header class="iande-gallery-item__header">
                    <button type="button" class="iande-button selected" aria-label="Remover" @click="removeItem(item)" v-if="isChecked(item)">
                        <Icon :icon="['fas', 'check-circle']"/>
                    </button>
                    <button type="button" class="iande-button" aria-label="Adicionar" @click="addItem(item)" v-else>
                        <Icon :icon="['fas', 'plus-circle']"/>
                    </button>
                    <span>{{ item.title }}</span>
                </header>
                <div class="iande-gallery-item__thumbnail" :style="{ backgroundImage: thumbnail(item) }"/>
            </div>
        </div>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import { dispatchIandeEvent } from '../utils/events'

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
                checkedItems: {},
            }
        },
        mounted () {
            console.log(this.$props)
        },
        methods: {
            addItem (item) {
                this.checkedItems = { ...this.checkedItems, [item.id]: true }
                dispatchIandeEvent('addItem', { item })
            },
            isChecked (item) {
                return Boolean(this.checkedItems[item.id])
            },
            thumbnail (item) {
                return `url(${item.thumbnail['full'][0]})`
            },
            removeItem (item) {
                this.checkedItems = { ...this.checkedItems, [item.id]: false }
                dispatchIandeEvent('removeItem', { item })
            },
        },
    }
</script>
