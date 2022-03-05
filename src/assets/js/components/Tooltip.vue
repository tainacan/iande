<template>
    <div class="iande-tooltip" v-tooltip="{ content: text, placement, trigger: 'hover click focus' }" :aria-label="__('Saiba mais', 'iande')">
        <Icon icon="circle-question"/>
    </div>
</template>

<script>
    import { VTooltip } from 'v-tooltip'

    export default {
        name: 'Tooltip',
        directives: {
            tooltip: VTooltip,
        },
        props: {
            placement: { type: String, default: 'auto' },
            text: { type: String, required: true },
        },
    }
</script>
