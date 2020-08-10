<template>
    <div class="container full-width steps-indicator">
        <div class="container narrow steps-indicator__row">
            <div class="steps-indicator__step" v-for="(label, i) of stepLabels" :key="i" :class="step > i && 'active'">
                <div class="steps-indicator__step-number">
                    <span v-if="step > i + 1" :aria-label="`${i + 1}, feito`">
                        <Icon icon="check"/>
                    </span>
                    <span v-else>{{ i + 1 }}</span>
                </div>
                <div class="steps-indicator__step-label">{{ label }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    export default {
        name: 'StepsIndicator',
        components: {
            Icon: FontAwesomeIcon,
        },
        props: {
            step: { type: Number, default: 0 }
        },
        computed: {
            stepLabels () {
                return ['Reserva', 'Detalhes', 'Confirmação']
            }
        }
    }
</script>