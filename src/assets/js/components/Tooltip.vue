<template>
    <div class="iande-tooltip" v-tooltip="{ content: text, placement, trigger: 'hover click focus' }" aria-label="Saiba mais">
        <Icon icon="question-circle"/>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { VTooltip } from 'v-tooltip'

    export default {
        name: 'Tooltip',
        components: {
            Icon: FontAwesomeIcon,
        },
        directives: {
            tooltip: VTooltip
        },
        props: {
            placement: { type: String, default: 'auto' },
            text: { type: String, required: true }
        }
    }
</script>
