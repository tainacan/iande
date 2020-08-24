<template>
    <div class="iande-steps" :class="inline ? 'inline' : 'iande-container full-width'">
        <div class="iande-steps__row" :class="inline || 'iande-container narrow'">
            <div class="iande-steps__step" v-for="(label, i) of stepLabels" :key="i" :class="step > i && 'active'">
                <div class="iande-steps__step-number">
                    <span v-if="step > i + 1" :aria-label="`${i + 1}, feito`">
                        <Icon icon="check"/>
                    </span>
                    <span v-else>{{ i + 1 }}</span>
                </div>
                <div class="iande-steps__step-label">{{ label }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import { constant } from '../utils'

    export default {
        name: 'StepsIndicator',
        components: {
            Icon: FontAwesomeIcon,
        },
        props: {
            inline: { type: Boolean, default: false },
            status: { type: String, dfault: 'draft' },
            step: { type: Number, default: 0 },
        },
        computed: {
            stepLabels: constant(['Reserva', 'Detalhes', 'Confirmação'])
        }
    }
</script>