<template>
    <div class="iande-modal__wrapper" v-if="isOpen">
        <div class="iande-modal">
            Eu sou um Modal
            <slot/>
        </div>
    </div>
    <div v-else></div>
</template>

<script>
    export default {
        name: 'Modal',
        data () {
            return {
                isOpen: false
            }
        },
        methods: {
            hide () {
                this.isOpen = false
            },
            show () {
                this.isOpen = true
            },
        }
    }
</script>