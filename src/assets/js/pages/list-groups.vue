<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Calendário</h1>
            <GroupsAgenda :exhibitions="exhibitions" :groups="groups"/>
        </div>
    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import GroupsAgenda from '../components/GroupsCalendar'
    import { api } from '../utils'

    export default {
        name: 'ListGroupsPage',
        components: {
            GroupsAgenda,
            Icon: FontAwesomeIcon,
        },
        data () {
            return {
                exhibitions: [],
                groups: [],
            }
        },
        async created () {
            try {
                const exhibitions = await api.get('exhibition/list')
                this.exhibitions = exhibitions
                const groups = await api.get('group/list') ?? []
                this.groups = groups
            } catch (err) {
                console.error(err)
            }
        }
    }
</script>
