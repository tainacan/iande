<template>
    <div>
        <ItineraryToolbar/>
    </div>
</template>

<script>
    import ItineraryToolbar from '@components/ItineraryToolbar.vue'
    import { qs } from '@utils'

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
            if (qs.has('ID')) {
                try {

                } catch (err) {
                    console.error(err)
                }
            }
        }
    }
</script>
