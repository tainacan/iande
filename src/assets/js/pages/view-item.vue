<template>
    <div>
        <ItineraryToolbar/>
    </div>
</template>

<script>
    import ItineraryToolbar from '@components/ItineraryToolbar.vue'

    export default {
        name: 'ViewItemPage',
        components: {
            ItineraryToolbar,
        },
        data () {
            return {

            }
        },
        async beforeMount () {
            const qs = new URLSearchParams(window.location.search)
            if (qs.has('ID')) {
                try {

                } catch (err) {
                    console.error(err)
                }
            }
        }
    }
</script>
