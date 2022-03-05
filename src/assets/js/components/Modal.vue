<template>
    <div class="iande-modal__wrapper" v-if="isOpen">
        <GlobalEvents @keyup.esc="close"/>
        <div class="iande-modal" :class="{ narrow }" role="dialog" aria-modal="true" :aria-label="label" tabindex="-1">
            <div class="iande-modal__header">
                <div class="iande-modal__close" role="button" tabindex="0" ref="button" :aria-label="__('Fechar', 'iande')" @click="close" @keypress.enter="close">
                    <Icon icon="xmark"/>
                </div>
            </div>
            <div class="iande-modal__body">
                <slot/>
            </div>
        </div>
    </div>
    <div v-else/>
</template>

<script>
    import GlobalEvents from 'vue-global-events'

    export default {
        name: 'Modal',
        components: {
            GlobalEvents,
        },
        props: {
            label: { type: String, required: true },
            narrow: { type: Boolean, default: false },
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
