<template>
    <div class="iande-itinerary" v-if="itinerary">
        <div class="iande-itinerary-cover" v-if="cover">
            <div class="iande-itinerary-cover__main" :style="{ '--itinerary-cover': `url(${itinerary.cover})` }">
                <div class="iande-container">
                    <div>
                        <div class="iande-itinerary-cover__lead">{{ __('Roteiro virtual', 'iande') }}</div>
                        <h1 class="iande-itinerary-cover__title">{{ itinerary.title }}</h1>
                        <p class="iande-itinerary-cover__description">{{ formatText(itinerary.description) }}</p>
                        <div class="iande-itinerary-cover__share" v-if="itinerary.shareable === 'yes'">
                            <span>{{ __('Compartilhe', 'iande') }}</span>
                            <ul>
                                <li>
                                    <a :href="facebookLink" target="_blank" aria-label="Facebook">
                                        <Icon :icon="['fab', 'facebook-f']"/>
                                    </a>
                                </li>
                                <li>
                                    <a :href="twitterLink" target="_blank" aria-label="Twitter">
                                        <Icon :icon="['fab', 'twitter']"/>
                                    </a>
                                </li>
                                <li>
                                    <a :href="whatsappLink" target="_blank" aria-label="WhatsApp">
                                        <Icon :icon="['fab', 'whatsapp']"/>
                                    </a>
                                </li>
                                <li v-if="shareAvailable">
                                    <a href="javascript:void(0)" role="button" tabindex="0" :aria-label="__('Compartilhar', 'iande')" @click="share" @keypress.enter="share">
                                        <Icon icon="share-alt"/>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <button @click="cover = false">
                            {{ __('Iniciar', 'iande') }}
                            <Icon icon="angle-right"/>
                        </button>
                    </div>
                </div>
            </div>
            <div class="iande-itinerary-cover__credits">
                <div class="iande-container">
                    <div>{{ __('Roteiro criado por', 'iande') }} {{ itinerary.user_name }}</div>
                    <div>{{ __('Obras pertencem à coleção de', 'iande') }} <b>{{ $iande.siteName }}</b></div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="iande-itinerary-header">
                <div class="iande-itinerary-toolbar">
                    <div class="iande-container iande-itinerary-toolbar__row">
                        <div>
                            <span>{{ __('Roteiro de visita virtual', 'iande') }}</span>
                        </div>
                        <div>
                            <span>{{ itinerary.title }}</span>
                        </div>
                        <div>
                            <a :href="$iandeUrl('itinerary/list')">
                                {{ __('Fechar', 'iande') }}
                                <Icon icon="times"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="iande-container">
                <div class="iande-itinerary-navigation">
                    <button :aria-label="page === 0 ? __('Voltar à capa', 'iande') : __('Página anterior', 'iande') " @click="previousPage">
                        <Icon icon="angle-left"/>
                    </button>
                    <progress :value="page + 1" :max="itinerary.items.length">{{ page + 1 }} / {{ itinerary.items.length }}</progress>
                    <button :aria-label="page === itinerary.items.length - 1 ? __('Voltar à capa', 'iande') : __('Próxima página', 'iande') " @click="nextPage">
                        <Icon icon="angle-right"/>
                    </button>
                </div>

                <div class="iande-itinerary-page" :class="`layout-${itinerary.layout}`" v-if="currentItem">
                    <a class="iande-itinerary-page__image" :href="currentMeta.url">
                        <img :src="currentImage.thumbnails.large[0]" :alt="currentImage.alt_text">
                    </a>
                    <div class="iande-itinerary-page__details">
                        <div class="iande-itinerary-page__details-main">
                            <h2 class="iande-itinerary-page__title">{{ currentMeta.title }}</h2>
                            <p class="iande-itinerary-page__description">{{ formatText(currentItem.items_description || currentMeta.description) }}</p>
                        </div>
                        <button class="iande-button primary">
                            <Icon icon="info-circle"/>
                            {{ __('Ver ficha completa da obra', 'iande') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { api, qs } from '@utils'

    export default {
        name: 'ViewItineraryPage',
        data () {
            return {
                cover: true,
                itemsCache: {},
                itinerary: null,
                page: 0,
                shareAvailable: true,
            }
        },
        computed: {
            currentImage () {
                return this.currentMeta?.attachments.find(att => att.media_type === 'image')
            },
            currentItem () {
                return this.itinerary.items[this.page]
            },
            currentMeta () {
                return this.itemsCache[this.page] ?? null
            },
            facebookLink () {
                return `https://www.facebook.com/sharer/sharer.php?quote=${window.encodeURIComponent(this.shareText)}&u=${window.location.href}`
            },
            shareText () {
                return `${this.itinerary.title}\n\n${this.itinerary.description}`
            },
            twitterLink () {
                return `https://twitter.com/intent/tweet?text=${window.encodeURIComponent(this.shareText)}&url=${window.location.href}`
            },
            whatsappLink () {
                return `whatsapp://send?text=${window.encodeURIComponent(`${this.shareText}\n\n${window.location.href}`)}`
            },
        },
        watch: {
            page () {
                this.fetchItem(this.page)
            },
        },
        async beforeMount () {
            this.shareAvailable = typeof window.navigator.share === 'function'
            if (qs.has('ID')) {
                try {
                    const itineraryID = qs.get('ID')
                    const itinerary = await api.get(`itinerary/get/?ID=${itineraryID}&public=1`)
                    this.itinerary = itinerary
                    await this.fetchItem(0)
                } catch (err) {
                    console.error(err)
                }
            }
        },
        methods: {
            async fetchItem (page) {
                if (this.itemsCache[page]) {
                    return
                } else {
                    const itemId = this.itinerary.items[page].items_id
                    const [item, attachments] = await Promise.all([
                        api.get(`${this.$iande.tainacanUrl}/items/${itemId}`),
                        api.get(`${this.$iande.tainacanUrl}/items/${itemId}/attachments`),
                    ])
                    item.attachments = attachments
                    this.$set(this.itemsCache, page, item)
                }
            },
            formatText (text) {
                return text.trim().replace(/\r?\n/g, '<br/>')
            },
            gotoCover () {
                this.cover = true
                this.page = 0
            },
            nextPage () {
                if (this.page === this.itinerary.items.length - 1) {
                    this.gotoCover()
                } else {
                    this.page++
                }
            },
            previousPage () {
                if (this.page === 0) {
                    this.gotoCover()
                } else {
                    this.page--
                }
            },
            share () {
                window.navigator.share({
                    text: this.itinerary.description,
                    title: this.itinerary.title,
                    url: window.location.href,
                })
            },
        },
    }
</script>
