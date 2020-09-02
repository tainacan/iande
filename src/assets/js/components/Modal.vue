<template>
    <div class="iande-modal__wrapper" v-if="isOpen">
        <div class="iande-modal" role="alertdialog">
            <div class="iande-modal__header">
                <div class="iande-modal__close" role="button" tabindex="0" ref="button" aria-label="Fechar">
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

    export default {
        name: 'Modal',
        components: {
            Icon: FontAwesomeIcon,
        },
        data () {
            return {
                isOpen: false
            }
        },
        methods: {
            close () {
                this.isOpen = false
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