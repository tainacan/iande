<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Suas instituições</h1>
            <div class="iande-container narrow iande-form">
                <a class="iande-button outline" :href="`${iandeUrl}/institution/create`">
                    <Icon icon="plus-circle"/>
                    Criar nova instituição
                </a>
            </div>
        </div>

    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { sync } from 'vuex-pathify'

    import { api, constant } from '../utils'

    export default {
        name: 'ListInstitutionsPage',
        components: {
            Icon: FontAwesomeIcon,
        },
        computed: {
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            institutions: sync('institutions/list'),
        },
        async created () {
            if (this.institutions.length === 0) {
                const institutions = await api.get('institution/list')
                this.institutions = institutions
            }
        }
    }
</script>