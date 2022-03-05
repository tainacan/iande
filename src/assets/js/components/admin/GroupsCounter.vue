<template>
    <div class="iande-admin-agenda__counters">
        <div class="iande-admin-agenda__counter publish" :title="sprintf(_n('%s grupo confirmado', '%s grupos confirmados', confirmedGroups, 'iande'), confirmedGroups)" v-if="confirmedGroups > 0">
            <Icon icon="circle-check"/>
            {{ confirmedGroups }}
        </div>
        <div class="iande-admin-agenda__counter pending" :title="sprintf(_n('%s grupo pendente', '%s grupos pendentes', pendingGroups, 'iande'), pendingGroups)" v-if="pendingGroups > 0">
            <Icon icon="circle-exclamation"/>
            {{ pendingGroups }}
        </div>
    </div>
</template>

<script>
    export default {
        name: 'GroupsCounter',
        props: {
            groups: { type: Array, required: true },
        },
        computed: {
            confirmedGroups () {
                return this.groups.filter(group => group.appointment.post_status === 'publish').length
            },
            pendingGroups () {
                return this.groups.filter(group => group.appointment.post_status === 'pending').length
            },
        },
    }
</script>
