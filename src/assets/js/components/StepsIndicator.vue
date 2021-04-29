<template>
    <div class="iande-steps" :class="inline ? 'inline' : 'iande-container full-width'">
        <div class="iande-steps__row" :class="inline || 'iande-container narrow'">
            <div class="iande-steps__step" v-for="i of 2" :key="i" :class="step >= i && 'active'">
                <div class="iande-steps__step-number">
                    <span v-if="step > i" :aria-label="sprintf(__('%s, concluído', 'iande'), i)">
                        <Icon icon="check"/>
                    </span>
                    <span v-else-if="step === i && status === 'canceled'" :aria-label="sprintf(__('%s, cancelado', 'iande'), i)">
                        <Icon icon="times"/>
                    </span>
                    <span v-else>{{ i }}</span>
                </div>
                <div class="iande-steps__step-label">{{ stepLabels[i - 1] }}</div>
            </div>
            <div class="iande-steps__step" :key="3" :class="step === 3 && status !== 'draft' && 'active'">
                <div class="iande-steps__step-number">
                    <span v-if="step === 3 && status === 'publish'" :aria-label="__('3, confirmado', 'iande')">
                        <Icon icon="check"/>
                    </span>
                    <span v-else-if="step === 3 && status === 'canceled'" :aria-label="__('3, cancelado', 'iande')">
                        <Icon icon="times"/>
                    </span>
                    <span v-else-if="step === 3 && status === 'pending'" :aria-label="__('3, aguardando confirmação', 'iande')">3</span>
                    <span v-else>3</span>
                </div>
                <div class="iande-steps__step-label">
                    {{ stepLabels[2] }}
                    <Tooltip v-if="reason" :text="reasonText"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Tooltip from './Tooltip.vue'
    import { sprintf, __ } from '../plugins/wp-i18n'
    import { constant } from '../utils'

    export default {
        name: 'StepsIndicator',
        components: {
            Tooltip
        },
        props: {
            inline: { type: Boolean, default: false },
            reason: { type: null, default: '' },
            status: { type: String, default: 'draft' },
            step: { type: Number, default: 0 },
        },
        computed: {
            reasonText () {
                return sprintf(__('<p><b>Este agendamento não foi confirmado</b></p><p>%s</p>', 'iande'), this.reason)
            },
            stepLabels: constant([__('Reserva', 'iande'), __('Detalhes', 'iande'), __('Confirmação', 'iande')])
        }
    }
</script>