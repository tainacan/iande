<template>
    <div>
        <ItineraryToolbar/>
        <div class="iande-container mt-lg iande-stack stack-lg"/>
        <ItineraryWelcomeModal ref="modal"/>
    </div>
</template>

<script>
    import ItineraryToolbar from '@components/ItineraryToolbar.vue'
    import ItineraryWelcomeModal from '@components/ItineraryWelcomeModal.vue'

    export default {
        name: 'EditItineraryPage',
        components: {
            ItineraryToolbar,
            ItineraryWelcomeModal,
        },
        mounted () {
            try {
                const modalShown = window.localStorage.getItem('iande.itineraryWelcome')
                if (!modalShown) {
                    this.$nextTick(() => {
                        this.$refs.modal.open()
                        window.localStorage.setItem('iande.itineraryWelcome', 'shown')
                    })
                }
            } catch (err) {
                console.error(err)
            }
        },
    }
</script>
