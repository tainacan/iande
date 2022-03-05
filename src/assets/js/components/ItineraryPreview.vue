<template>
    <article class="iande-itinerary-preview">
        <div class="iande-itinerary-preview__cover">
            <img :src="cover" alt="">
            <a :href="$iandeUrl(`itinerary/edit/?ID=${itinerary.ID}`)" :aria-label="__('Editar', 'iande')" v-if="editable">
                <Icon icon="pen"/>
            </a>
        </div>
        <div class="iande-itinerary-preview__content">
            <h3><a :href="$iandeUrl(`itinerary/view/?ID=${itinerary.ID}`)">{{ itinerary.title }}</a></h3>
            <div class="iande-itinerary-preview__bar">
                <span>{{ sprintf(_n('%s item', '%s itens', numItems, 'iande'), numItems) }}</span>
                <span>
                    <span :aria-label="__('Visualizações', 'iande')" :title="__('Visualizações', 'iande')">
                        <Icon icon="eye"/>
                    </span>
                    <span>{{ itinerary.views || 0 }}</span>
                </span>
                <span @click="like">
                    <span :aria-label="__('Gostou', 'iande')" :title="__('Gostou', 'iande')" v-if="itinerary.liked">
                        <Icon icon="star" class="gold"/>
                    </span>
                    <span :aria-label="__('Gostar', 'iande')" :title="__('Gostar', 'iande')" v-else>
                        <Icon :icon="['far', 'star']"/>
                    </span>
                    <span>{{ itinerary.likes || 0 }}</span>
                </span>
            </div>
            <p>{{ itinerary.description }}</p>
        </div>
    </article>
</template>

<script>
    import { get } from 'vuex-pathify'

    import { api } from '@utils'

    export default {
        name: 'ItineraryPreview',
        props: {
            itinerary: { type: Object, required: true },
        },
        computed: {
            cover () {
                return this.itinerary.cover || `${this.$iande.iandePath}assets/img/cover-placeholder.png`
            },
            editable () {
                return this.user.ID == this.itinerary.user_id
            },
            numItems () {
                return this.itinerary.items ? this.itinerary.items.length : 0
            },
            user: get('users/current'),
        },
        methods: {
            async like() {
                const result = await api.post(`itinerary/like/?ID=${this.itinerary.ID}`)
                if (result) {
                    this.itinerary.liked = true
                    this.itinerary.likes++
                } else {
                    this.itinerary.liked = false
                    this.itinerary.likes--
                }
            }
        },
    }
</script>
