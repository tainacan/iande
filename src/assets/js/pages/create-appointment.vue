<template>
    <article>
        <StepsIndicator :step="1"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="saveStep">
                <keep-alive>
                    <VisitDate v-if="screen === 1"/>
                </keep-alive>

                <div class="iande-form-grid">
                    <button class="iande-button solid" type="button" v-if="screen > 1" @click="screen -= 1">
                        <Icon icon="angle-left"/>
                        Voltar
                    </button>
                    <div v-else></div>
                    <button class="iande-button primary" type="submit">
                        Avançar
                        <Icon icon="angle-right"/>
                    </button>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import StepsIndicator from '../components/StepsIndicator.vue'
    import VisitDate from '../components/VisitDate.vue'

    export default {
        name: 'CreateAppointmentPage',
        components: {
            Icon: FontAwesomeIcon,
            StepsIndicator,
            VisitDate,
        },
        data () {
            return {
                screen: 1
            }
        },
        methods: {
            saveStep () {
                if (this.screen <= 4) {
                    this.screen++
                }
            },
            setScreen (num) {
                this.screen = num
            }
        }
    }
</script>
