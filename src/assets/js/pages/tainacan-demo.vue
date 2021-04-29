<template>
    <div class="iande-container mt-lg iande-stack stack-lg">
        <h1>Tainacan demo</h1>
        <p>{{ sprintf(_n('%s item', '%s itens', items.length, 'iande'), items.length) }}</p>
    </div>
</template>

<script>
    import { onIandeEvent } from '../utils/events'

    export default {
        name: 'TainacanDemoPage',
        data () {
            return {
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
    }
</script>
