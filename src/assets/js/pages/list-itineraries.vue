<template>
    <article id="iande-list-itineraries" class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Roteiros virtuais', 'iande') }}</h1>

            <div class="iande-appointments-toolbar">
                <AppointmentsFilter id="status" :label="__('Visualizando', 'iande')" :options="statusOptions" v-model="filter"/>
                <a class="iande-button small outline" :href="$iandeUrl('itinerary/create')">
                    <Icon icon="plus-circle"/>
                    {{ __('Criar novo roteiro', 'iande') }}
                </a>
            </div>

            <section class="iande-container iande-stack stack-md" v-if="userItineraries.length > 0">
                <h2>{{ __('Seus roteiros', 'iande') }}</h2>
                <div class="iande-itineraries-preview">
                    <ItineraryPreview v-for="itinerary of userItineraries" :key="itinerary.ID" :itinerary="itinerary"/>
                </div>
            </section>

            <section class="iande-container iande-stack stack-md" v-if="publicItineraries.length > 0">
                <h2>{{ __('Roteiros públicos', 'iande') }}</h2>
                <div class="iande-itineraries-preview">
                    <ItineraryPreview v-for="itinerary of publicItineraries" :key="itinerary.ID" :itinerary="itinerary"/>
                </div>
            </section>

            <div class="iande-container narrow" v-if="totalAppointments > 0">
                <a class="iande-button outline" :href="$iandeUrl('itinerary/create')">
                    <Icon icon="plus-circle"/>
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
    import { api, constant, sortBy } from '@utils'

    export default {
        name: 'ListItinerariesPage',
        components: {
            AppointmentsFilter,
            ItineraryPreview,
        },
        data () {
            return {
                filter: 'publish',
                publicItineraries: [],
                userItineraries: [],
            }
        },
        computed: {
            statusOptions: constant([
                { label: __('Publicados', 'iande'), value: 'publish' },
                { label: __('Rascunhos', 'iande'), value: 'draft' },
            ]),
            totalAppointments () {
                return this.publicItineraries.length + this.userItineraries.length
            },
        },
        async created () {
            try {
                const [publishedItineraties, userItineraries] = await Promise.all([
                    api.get('itinerary/list_published'),
                    api.get('itinerary/list'),
                ])
                this.publicItineraries = publishedItineraties
                this.userItineraries = userItineraries
            } catch (err) {
                console.error(err)
            }
        },
    }
</script>
