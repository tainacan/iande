<template>
    <Modal class="iande-group-modal" ref="modal" :label="__('Detalhes do grupo', 'iande')" @close="close">
        <GroupDetails :educators="educators" :group="group" v-if="group"/>
    </Modal>
</template>

<script>
    import GroupDetails from '@components/GroupDetails.vue'
    import Modal from '@components/Modal.vue'

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
