<template>
    <article class="mt-lg">
        <div class="iande-container iande-stack stack-lg">
            <h1>{{ __('Suas instituições', 'iande') }}</h1>
            <div class="iande-institutions">
                <InstitutionDetails v-for="(institution, n) of institutions" :key="institution.ID" :institution="institution" :n="n + 1"/>
            </div>
            <div class="iande-container narrow">
                <a class="iande-button outline" :href="$iandeUrl('institution/create')">
                    <Icon icon="circle-plus"/>
                    {{ __('Cadastrar nova instituição', 'iande') }}
                </a>
            </div>
        </div>

    </article>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import InstitutionDetails from '@components/InstitutionDetails.vue'
    import { api } from '@utils'

    export default {
        name: 'ListInstitutionsPage',
        components: {
            InstitutionDetails,
        },
        computed: {
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
