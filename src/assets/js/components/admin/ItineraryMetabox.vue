<template>
    <div class="iande-itinerary-metabox">
        <div class="components-base-control__field">
            <label for="tainacan-collection" class="components-input-control__label">{{ __('Coleção', 'iande') }}</label>
            <select name="tainacan-collection" id="tainacan-collection" class="components-select-control__input" v-model="form.collection">
                <option :key="0" :value="null">{{ __('Todo o repositório', 'iande') }}</option>
                <option v-for="collection of collections" :key="collection.id" :value="collection.id">{{ collection.name }}</option>
            </select>
        </div>

        <div class="components-base-control__field">
            <label for="tainacan-metakey" class="components-input-control__label">{{ __('Filtro', 'iande') }}</label>
            <select name="tainacan-metakey" id="tainacan-metakey" class="components-select-control__input" v-model="form.metakey">
                <option :key="0" :value="null">{{ __('Sem filtro', 'iande') }}</option>
                <option v-for="metakey of metadata" :key="metakey.id" :value="metakey.id">{{ __(metakey.name, 'iande') }}</option>
            </select>
        </div>

        <div class="components-base-control__field" v-show="form.metakey">
            <label for="tainacan-metavalue" class="components-input-control__label">{{ __('Filtro', 'iande') }}</label>
            <select name="tainacan-metavalue" id="tainacan-metavalue" class="components-select-control__input" v-model="form.metavalue">
                <option v-for="metavalue of terms" :key="metavalue.value" :value="metavalue.value">{{ metavalue.label }} ({{ metavalue.total_items }})</option>
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
                collections: [],
                facetsCache: {},
                form: {
                    collection: null,
                    metakey: null,
                    metavalue: null,
                },
                globalMetadata: [],
                metadataCache: {},
            }
        },
        computed: {
            metadata () {
                if (this.form.collection) {
                    return this.metadataCache[this.form.collection] ?? []
                } else {
                    return this.globalMetadata
                }
            },
            terms () {
                if (this.form.metakey) {
                    return this.facetsCache[this.form.metakey] ?? []
                } else {
                    return []
                }
            },
        },
        watch: {
            async 'form.collection' () {
                const { collection } = this.form
                if (collection) {
                    if (!this.metadataCache[collection]) {
                        try {
                            const metadata = await api.get(`${tainacanUrl}/collection/${collection}/metadata`)
                            this.$set(this.metadataCache, collection, metadata)
                        } catch (err) {
                            console.error(err)
                        }
                    }
                }
            },
            async 'form.metakey' () {
                const { collection, metakey } = this.form
                if (metakey) {
                    if (!this.facetsCache[metakey]) {
                        try {
                            const fragment = collection ? `collection/${collection}/facets/${metakey}` : `facets/${metakey}`
                            const facets = await api.get(`${tainacanUrl}/${fragment}?hideempty=0`)
                            this.$set(this.facetsCache, metakey, facets.values)
                        } catch (err) {
                            console.error(err)
                        }
                    }
                }
            },
            metadata () {
                if (!this.metadata.find(metakey => metakey.id === this.form.metakey)) {
                    this.form.metakey = null
                    this.form.metavalue = null
                }
            },
            terms () {
                this.form.metavalue = null
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
