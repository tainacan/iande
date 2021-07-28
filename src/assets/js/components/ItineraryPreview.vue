<template>
    <article class="iande-itinerary-preview">
        <div class="iande-itinerary-preview__cover">
            <img :src="cover" alt="">
            <a :href="$iandeUrl(`itinerary/edit/?ID=${itinerary.ID}`)" :aria-label="__('Editar', 'iande')" v-if="editable">
                <Icon icon="pencil-alt"/>
            </a>
        </div>
        <div class="iande-itinerary-preview__content">
            <h3>{{ itinerary.title }}</h3>
            <div class="iande-itinerary-preview__bar">
                <span>{{ sprintf(_n('%s item', '%s itens', numItems, 'iande'), numItems) }}</span>
            </div>
            <p>{{ itinerary.description }}</p>
        </div>
    </article>
</template>

<script>
    import { get } from 'vuex-pathify'

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
                return this.itinerary.items.length
            },
            user: get('users/current'),
        },
    }
</script>
