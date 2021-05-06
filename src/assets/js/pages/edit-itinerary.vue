<template>
    <div>
        <ItineraryToolbar/>
        <div class="iande-container mt-lg iande-stack stack-lg">
            <h1>Tainacan demo</h1>
        </div>
        <ItineraryWelcomeModal ref="modal"/>
    </div>
</template>

<script>
    import ItineraryToolbar from '../components/ItineraryToolbar.vue'
    import ItineraryWelcomeModal from '../components/ItineraryWelcomeModal.vue'

    export default {
        name: 'EditItineraryPage',
        components: {
            ItineraryToolbar,
            ItineraryWelcomeModal,
        },
        mounted () {
            this.$nextTick(() => {
                this.$refs.modal.open()
            })
        },
    }
</script>
