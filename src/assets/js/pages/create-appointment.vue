<template>
    <div>
        <StepsIndicator :step="1"/>
    </div>
</template>

<script>
    import StepsIndicator from '../components/StepsIndicator.vue'

    export default {
        name: 'CreateAppointmentPage',
        components: {
            StepsIndicator,
        },
        data () {
            return {
                screen: 1
            }
        }
    }
</script>
