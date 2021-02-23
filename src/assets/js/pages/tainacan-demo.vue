<template>
    <div class="iande-container mt-lg">
        <h1>Tainacan demo</h1>
        <portal-target name="tainacan-view"/>
    </div>
</template>

<script>
    import { onIandeEvent } from '../utils/events'

    export default {
        name: 'TainacanDemoPage',
        data () {
            return {
                unsubscribe: null,
            }
        },
        mounted () {
            this.unsubscribe = onIandeEvent((type, payload) => {
                console.log(type, payload)
            })
        },
        beforeDestroy () {
            if (this.unsubscribe) {
                this.unsubscribe()
            }
        },
    }
</script>
