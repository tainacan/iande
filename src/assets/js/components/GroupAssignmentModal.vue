<template>
    <Modal ref="modal" @close="close">
        <GroupDetails :educators="educators" :group="group" v-if="group"/>
    </Modal>
</template>

<script>
    import GroupDetails from './GroupDetails.vue'
    import Modal from './Modal.vue'

    export default {
        name: 'GroupAssignmentModal',
        components: {
            GroupDetails,
            Modal,
        },
        props: {
            educators: { type: Array, default: () => [] },
        },
        data () {
            return {
                group: null,
            }
        },
        methods: {
            close () {
                if (this.$refs.modal.isOpen) {
                    this.$refs.modal.close()
                }
                this.group = null
                this.$emit('close', this.group)
            },
            open (group) {
                this.group = group
                this.$refs.modal.open()
            },
        }
    }
</script>
