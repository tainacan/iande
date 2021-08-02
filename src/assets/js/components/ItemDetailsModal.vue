<template>
    <Modal ref="modal" class="iande-tainacan-modal" :label="__('Ficha da obra', 'iande')" @close="close">
        <header class="iande-tainacan-modal__header">
            <h1>{{ __('Mais detalhes sobre a obra', 'iande') }}</h1>
            <a :href="item.url">
                <span>{{ _x('Ver item no', 'tainacan', 'iande') }} <b>Tainacan</b></span>
                <img :src="`${$iande.iandePath}assets/img/tainacan-logo_short.png`" alt="">
            </a>
        </header>

        <main class="iande-tainacan-modal__details">
            <dl v-if="image">
                <dt>{{ _x('Miniatura', 'tainacan', 'iande') }}</dt>
                <dd>
                    <a :href="image.url" target="_blank">
                        <img :src="image.thumbnails.thumbnail[0]" :alt="_x('Miniatura', 'tainacan', 'iande')">
                    </a>
                </dd>
            </dl>
            <template v-for="(metadatum, key) of item.metadata">
                <dl :key="key" v-if="metadatum.value_as_html">
                    <dt>{{ _x(metadatum.name, 'tainacan', 'iande') }}</dt>
                    <dd v-html="metadatum.value_as_html"/>
                </dl>
            </template>
        </main>

        <footer class="iande-tainacan-modal__footer">
            <a :href="item.url">
                <span>{{ _x('Ver item no', 'tainacan', 'iande') }} <b>Tainacan</b></span>
                <img :src="`${$iande.iandePath}assets/img/tainacan-logo_short.png`" alt="">
            </a>
        </footer>
    </Modal>
</template>

<script>
    import Modal from '@components/Modal.vue'

    export default {
        name: 'ItemDetailsModal',
        components: {
            Modal,
        },
        props: {
            item: { type: Object, required: true },
        },
        computed: {
            image () {
                return this.item.attachments.find(att => att.media_type === 'image')
            },
        },
        methods: {
            close () {
                if (this.$refs.modal.isOpen) {
                    this.$refs.modal.close()
                }
            },
            open () {
                this.$refs.modal.open()
            },
        },
    }
</script>
