<template>
    <div>
        <div class="iande-itinerary-header">
            <div class="iande-itinerary-toolbar">
            </div>
        </div>
        <div class="iande-itinerary" v-if="itinerary">
            <div class="iande-itinerary-cover" v-if="cover">
                <div class="iande-itinerary-cover__main" :style="{ '--itinerary-cover': `url(${itinerary.cover})` }">
                    <div class="iande-container">
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
                itemsMap: {},
                itinerary: null,
                page: 0,
                shareAvailable: true,
            }
        },
        computed: {
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
        async beforeMount () {
            this.shareAvailable = typeof window.navigator.share === 'function'
            if (qs.has('ID')) {
                try {
                    const itineraryID = qs.get('ID')
                    const itinerary = await api.get(`itinerary/get/?ID=${itineraryID}&public=1`)
                    this.itinerary = itinerary
                } catch (err) {
                    console.error(err)
                }
            }
        },
        methods: {
            formatText (text) {
                return text.trim().replace(/\r?\n/g, '<br/>')
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
