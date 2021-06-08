<template>
    <div class="iande-itinerary-metabox">
        <div class="components-base-control__field">
            <label for="tainacan-collection" class="components-input-control__label">{{ __('Coleção', 'iande') }}</label>
            <select name="tainacan-collection" id="tainacan-collection" class="components-select-control__input" v-model="collection">
                <option :key="0" :value="null">{{ __('Todo o repositório', 'iande') }}</option>
                <option v-for="collection of collections" :key="collection.id" :value="collection.id">{{ collection.name }}</option>
            </select>
        </div>

        <div class="components-base-control__field">
            <label for="tainacan-metadatum" class="components-input-control__label">{{ __('Filtro', 'iande') }}</label>
            <select name="tainacan-metadatum" id="tainacan-metadatum" class="components-select-control__input" v-model="metadatum">
                <option :key="0" :value="null">{{ __('Sem filtro', 'iande') }}</option>
                <option v-for="metadatum of metadata" :key="metadatum.id" :value="metadatum.id">{{ metadatum.name }}</option>
            </select>
        </div>
    </div>
</template>

<script>
    import { api } from '@utils'

    const tainacanUrl = window.IandeSettings.tainacanUrl

    export default {
        name: 'ItineraryMetabox',
        props: {
            id: { type: [Number, String], required: true },
        },
        data () {
            return {
                collection: null,
                collections: [],
                globalMetadata: [],
                metadatum: null,
                metadataCache: {},
            }
        },
        computed: {
            metadata () {
                if (this.collection) {
                    return this.metadataCache[this.collection] || []
                } else {
                    return this.globalMetadata
                }
            },
        },
        watch: {
            async collection () {
                if (this.collection) {
                    if (!this.metadataCache[this.collection]) {
                        try {
                            const metadata = await api.get(`${tainacanUrl}/collection/${this.collection}/metadata`)
                            this.$set(this.metadataCache, this.collection, metadata)
                        } catch (err) {
                            console.error(err)
                        }
                    }
                }
            },
        },
        async beforeMount () {
            try {
                const [collections, metadata] = await Promise.all([
                    api.get(`${tainacanUrl}/collections`),
                    api.get(`${tainacanUrl}/metadata`),
                ])
                this.collections = collections
                this.globalMetadata = metadata
            } catch (err) {
                console.error(err)
            }
        }
    }
</script>
