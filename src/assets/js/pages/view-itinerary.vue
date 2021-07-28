<template>
    <div>
        <div class="iande-itinerary-header">
            <div class="iande-itinerary-toolbar"/>
        </div>
        <pre>{{ itinerary }}</pre>
    </div>
</template>

<script>
    import { api, qs } from '@utils'

    export default {
        name: 'ViewItineraryPage',
        data () {
            return {
                itinerary: null,
            }
        },
        async beforeMount () {
            if (qs.has('ID')) {
                try {
                    const itineraryID = qs.get('ID')
                    const itinerary = await api.get(`itinerary/get/?ID=${itineraryID}`)
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
        },
    }
</script>
