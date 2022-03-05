<template>
    <div class="iande-status" :class="status">
        <template v-if="status === 'pending'">
            <Icon icon="spinner" spin/>
            <span>{{ _x('Aguardando confirmação', 'group', 'iande') }}</span>
        </template>
        <template v-else-if="status === 'canceled'">
            <Icon icon="circle-xmark"/>
            <span>{{ _x('Cancelado', 'group', 'iande') }} <Tooltip v-if="reason" :text="reasonText"/></span>
        </template>
        <template v-else-if="status === 'publish'">
            <Icon icon="circle-check"/>
            <span>{{ _x('Confirmado', 'group', 'iande') }}</span>
        </template>
    </div>
</template>

<script>
    import Tooltip from '@components/Tooltip.vue'
    import { sprintf, __ } from '@plugins/wp-i18n'

    export default {
        name: 'StatusIndicator',
        components: {
            Tooltip,
        },
        props: {
            reason: { type: null, default: '' },
            status: { type: String, default: 'draft' },
        },
        computed: {
            reasonText () {
                return sprintf(__('<p><b>Este agendamento não foi confirmado</b></p><p>%s</p>', 'iande'), this.reason)
            },
        }
    }
</script>
