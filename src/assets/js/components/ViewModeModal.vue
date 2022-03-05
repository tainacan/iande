<template>
    <div class="iande-modal__wrapper iande-tainacan-modal" v-if="isOpen">
        <GlobalEvents @keyup.esc="close"/>
        <div class="iande-modal" role="dialog" aria-modal="true" :aria-label="__('Ficha da obra', 'iande')" tabindex="-1">
            <div class="iande-modal__header">
                <div class="iande-modal__close" role="button" tabindex="0" ref="button" :aria-label="__('Fechar', 'iande')" @click="close" @keypress.enter="close">
                    <Icon icon="xmark"/>
                </div>
            </div>
            <div class="iande-modal__body">
                <header class="iande-tainacan-modal__header">
                    <h1>{{ __('Mais detalhes sobre a obra', 'iande') }}</h1>
                    <a :href="item.url">
                        <span>{{ _x('Ver item no', 'tainacan', 'iande') }} <b>Tainacan</b></span>
                        <img :src="`${iande.iandePath}assets/img/tainacan-logo_short.png`" alt="">
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
                        <img :src="`${iande.iandePath}assets/img/tainacan-logo_short.png`" alt="">
                    </a>
                </footer>
            </div>
        </div>
    </div>
    <div v-else/>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import GlobalEvents from 'vue-global-events'

    import { __, _x } from '@plugins/wp-i18n'

    /**
     * Esse component emula o component ItemDetailsModal, mas sem dependências às globais do Iandé
     */
    export default {
        name: 'ViewModeModal',
        components: {
            GlobalEvents,
            Icon: FontAwesomeIcon,
        },
        props: {
            item: { type: Object, default: false },
        },
        data () {
            return {
                isOpen: false,
            }
        },
        computed: {
            iande () {
                return window.IandeSettings
            },
            image () {
                return this.item.attachments.find(att => att.media_type === 'image')
            },
        },
        methods: {
            __,
            _x,
            close (emitEvent = true) {
                this.isOpen = false
                if (emitEvent) {
                    this.$emit('close')
                }
            },
            open () {
                this.isOpen = true
                this.$nextTick(() => {
                    this.$refs.button.focus()
                })
            },
        }
    }
</script>
