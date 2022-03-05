<template>
    <article id="iande-list-itineraries" class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Roteiros virtuais', 'iande') }}</h1>

            <div class="iande-appointments-toolbar">
                <AppointmentsFilter id="status" :label="__('Visualizando', 'iande')" :options="statusOptions" v-model="filter"/>
                <a class="iande-button small outline" :href="$iandeUrl('itinerary/create')">
                    <Icon icon="circle-plus"/>
                    {{ __('Criar novo roteiro', 'iande') }}
                </a>
            </div>

            <section class="iande-container iande-stack stack-md" v-if="filteredItineraries.length > 0">
                <h2>{{ __('Seus roteiros', 'iande') }}</h2>
                <div class="iande-itineraries-preview">
                    <ItineraryPreview v-for="itinerary of filteredItineraries" :key="itinerary.ID" :itinerary="itinerary"/>
                </div>
            </section>

            <section class="iande-container iande-stack stack-md" v-if="publicItineraries.length > 0">
                <h2>{{ __('Roteiros públicos', 'iande') }}</h2>
                <div class="iande-itineraries-preview">
                    <ItineraryPreview v-for="itinerary of publicItineraries" :key="itinerary.ID" :itinerary="itinerary"/>
                </div>
                <div class="iande-container narrow" v-if="page < maxPages">
                    <button class="iande-button primary" @click="fetchMoreItineraries">
                        {{ __('Carregar mais', 'iande') }}
                    </button>
                </div>
            </section>

            <div class="iande-container narrow" v-if="totalAppointments > 0">
                <a class="iande-button outline" :href="$iandeUrl('itinerary/create')">
                    <Icon icon="circle-plus"/>
                    {{ __('Criar novo roteiro', 'iande') }}
                </a>
            </div>
        </div>
    </article>
</template>

<script>
    import AppointmentsFilter from '@components/AppointmentsFilter.vue'
    import ItineraryPreview from '@components/ItineraryPreview.vue'
    import { __ } from '@plugins/wp-i18n'
    import { api, constant } from '@utils'

    export default {
        name: 'ListItinerariesPage',
        components: {
            AppointmentsFilter,
            ItineraryPreview,
        },
        data () {
            return {
                filter: 'publish',
                maxPages: 1,
                page: 1,
                publicItineraries: [],
                userItineraries: [],
            }
        },
        computed: {
            filteredItineraries () {
                if (this.filter === 'draft') {
                    return this.userItineraries.filter(itinerary => itinerary.post_status === 'draft')
                } else {
                    return this.userItineraries.filter(itinerary => itinerary.post_status === 'pending' || itinerary.post_status === 'publish')
                }
            },
            statusOptions: constant([
                { label: __('Publicados', 'iande'), value: 'publish' },
                { label: __('Rascunhos', 'iande'), value: 'draft' },
            ]),
            totalAppointments () {
                return this.filteredItineraries.length + this.publicItineraries.length
            },
        },
        async created () {
            try {
                const [publicItineraries, userItineraries] = await Promise.all([
                    api.get('itinerary/list_published/?page=1'),
                    api.get('itinerary/list'),
                ])
                this.maxPages = publicItineraries.num_pages
                this.publicItineraries = publicItineraries.items
                this.userItineraries = userItineraries
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            async fetchMoreItineraries () {
                try {
                    const page = this.page + 1
                    const publicItineraries = await api.get(`itinerary/list_published/?page=${page}`)
                    this.page = page
                    this.publicItineraries = [...this.publicItineraries, ...publicItineraries.items]
                } catch (err) {
                    console.error(err)
                }
            },
        }
    }
</script>
