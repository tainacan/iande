<template>
    <div class="iande-modal__wrapper" v-if="isOpen">
        <GlobalEvents @keyup.esc="close"/>
        <div class="iande-modal" role="dialog" aria-modal="true" :aria-label="label" tabindex="-1">
            <div class="iande-modal__header">
                <div class="iande-modal__close" role="button" tabindex="0" ref="button" aria-label="Fechar" @click="close" @keypress.enter="close">
                    <Icon icon="times"/>
                </div>
            </div>
            <slot/>
        </div>
    </div>
    <div v-else/>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import GlobalEvents from 'vue-global-events'

    export default {
        name: 'Modal',
        components: {
            GlobalEvents,
            Icon: FontAwesomeIcon,
        },
        props: {
            label: { type: String, default: 'Sucesso!' },
        },
        data () {
            return {
                isOpen: false
            }
        },
        methods: {
            close (emitEvent = true) {
                this.isOpen = false
                if (emitEvent) {
                    this.$emit('close')
                }
            },
            open () {
                this.isOpen = true
                this.$nextTick(() => {
                    this.$refs.button.focus()
                })
            },
        }
    }
</script>