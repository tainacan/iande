<template>
    <div class="iande-field">
        {{ value }}
    </div>
</template>

<script>
    import CustomField from './mixins/CustomField'
    import Repeater from './Repeater.vue'

    export default {
        name: 'GroupInfo',
        components: {
            Repeater,
        },
        mixins: [CustomField],
    }
</script>