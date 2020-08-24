<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>Suas instituições</h1>
            <div class="iande-institutions">
                <InstitutionDetails v-for="(institution, n) in institutions" :key="institution.ID" :institution="institution" :n="n + 1"/>
            </div>
            <div class="iande-container narrow">
                <a class="iande-button outline" :href="`${iandeUrl}/institution/create`">
                    <Icon icon="plus-circle"/>
                    Cadastrar nova instituição
                </a>
            </div>
        </div>

    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { sync } from 'vuex-pathify'

    import InstitutionDetails from '../components/InstitutionDetails.vue'
    import { api, constant } from '../utils'

    export default {
        name: 'ListInstitutionsPage',
        components: {
            Icon: FontAwesomeIcon,
            InstitutionDetails,
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