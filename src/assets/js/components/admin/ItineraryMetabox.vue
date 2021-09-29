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
            <label for="tainacan-metavalue" class="components-input-control__label">{{ __('Valor', 'iande') }}</label>
            <select name="tainacan-metavalue" id="tainacan-metavalue" class="components-select-control__input" v-model="form.metavalue">
                <option v-for="metavalue of terms" :key="metavalue.value" :value="metavalue.value">{{ metavalue.label }} ({{ metavalue.total_items }})</option>
            </select>
        </div>

        <input type="hidden" id="tainacan-meta" name="tainacan-meta" :value="JSON.stringify(form)">
    </div>
</template>

<script>
    import { api } from '@utils'

    const tainacanUrl = window.IandeSettings.tainacanUrl
    const TAXONOMIES_ONLY = 'metaquery[0][key]=metadata_type&metaquery[0][value]=Tainacan%5CMetadata_Types%5CTaxonomy';

    export default {
        name: 'ItineraryMetabox',
        props: {
            previous: { type: Object, required: true },
        },
        data () {
            return {
                collections: [],
                facetsCache: {},
                fetched: false,
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
            'form.collection' () {
                if (this.form.collection && !this.metadataCache[this.form.collection]) {
                    this.fetchMetadata(this.form)
                }
            },
            'form.metakey' () {
                if (this.form.metakey && !this.facetsCache[this.form.metakey]) {
                    this.fetchFacets(this.form)
                }
            },
            metadata () {
                if (this.fetched && !this.metadata.find(metakey => metakey.id === this.form.metakey)) {
                    this.form.metakey = null
                    this.form.metavalue = null
                }
            },
            terms () {
                if (this.fetched) {
                    this.form.metavalue = null
                }
            },
        },
        async created () {
            try {
                const [collections, metadata] = await Promise.all([
                    api.get(`${tainacanUrl}/collections`),
                    api.get(`${tainacanUrl}/metadata?${TAXONOMIES_ONLY}`),
                ])
                this.collections = collections
                this.globalMetadata = metadata

                if (this.previous) {
                    await Promise.all([
                        this.previous.collection ? this.fetchMetadata(this.previous) : null,
                        this.previous.metakey ? this.fetchFacets(this.previous) : null,
                    ])
                    this.form = this.previous
                }

                this.$nextTick(() => {
                    this.fetched = true
                })
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            async fetchFacets (form) {
                const { collection, metakey } = form
                try {
                    const fragment = collection ? `collection/${collection}/facets/${metakey}` : `facets/${metakey}`
                    const facets = await api.get(`${tainacanUrl}/${fragment}?hideempty=0`)
                    this.$set(this.facetsCache, metakey, facets.values)
                } catch (err) {
                    console.error(err)
                }
            },
            async fetchMetadata (form) {
                const { collection } = form
                try {
                    const metadata = await api.get(`${tainacanUrl}/collection/${collection}/metadata?${TAXONOMIES_ONLY}`)
                    this.$set(this.metadataCache, collection, metadata)
                } catch (err) {
                    console.error(err)
                }
            },
        },
    }
</script>
