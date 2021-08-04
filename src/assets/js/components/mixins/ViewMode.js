import { __ } from '@plugins/wp-i18n'
import { dispatchIandeEvent, onIandeEvent } from '@utils/events'

export default {
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
            selectedItem: null,
            unsubscribe: null,
        }
    },
    mounted () {
        const iandeEvents = {
            addItem: ({ id }) => {
                this.checkedItems = [...this.checkedItems, id]
            },
            removeItem: ({ id }) => {
                this.checkedItems = this.checkedItems.filter(item => item != id)
            },
            replaceItems: ({ ids }) => {
                this.checkedItems = [...ids]
            },
        }
        this.unsubscribe = onIandeEvent((type, payload) => {
            if (iandeEvents[type]) {
                iandeEvents[type](payload)
            }
        })
        dispatchIandeEvent('mountedViewMode')
    },
    beforeDestroy () {
        if (this.unsubscribe) {
            this.unsubscribe()
        }
    },
    methods: {
        __,
        addItem (item) {
            dispatchIandeEvent('addItem', { id: item.id })
        },
        getMeta (item, key) {
            if (typeof item[key] === 'string') {
                return item[key]
            }
            return item.metadata[key].value_as_html
        },
        isChecked (item) {
            return !!this.checkedItems.find(id => item.id == id)
        },
        async seeDetails ({ id }) {
            try {
                const [item, attachments] = await Promise.all([
                    window.fetch(`${window.IandeSettings.tainacanUrl}/items/${id}`).then(body => body.json()),
                    window.fetch(`${window.IandeSettings.tainacanUrl}/items/${id}/attachments`).then(body => body.json()),
                ])
                item.attachments = attachments
                this.selectedItem = item
                this.$nextTick(() => {
                    this.$refs.modal.open()
                })
            } catch (err) {
                console.error(err)
            }
        },
        removeItem (item) {
            dispatchIandeEvent('removeItem', { id: item.id })
        },
    },
}
