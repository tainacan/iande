<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent>
                <h1>{{ __('Criar roteiro virtual', 'iande') }}</h1>

                <div class="iande-form-error" v-if="formError">
                    <span>{{ __(formError, 'iande') }}</span>
                </div>

                <button class="iande-button primary" type="submit">
                    {{ __('Avançar', 'iande') }}
                    <Icon icon="angle-right"/>
                </button>
            </form>
        </div>
    </article>
</template>

<script>
    import { get } from 'vuex-pathify'

    import { api } from '../utils'

    export default {
        name: 'EditItineraryPage',
        data () {
            return {
                collections: [],
                formError: '',
            }
        },
        computed: {
            exhibitions: get('exhibitions/list'),
        },
        async beforeMount () {
            try {
                const collections = await api.get(`${this.$iande.siteUrl}/wp-json/tainacan/v2/collections`)
                this.collections = collections
            } catch (err) {
                this.formError = err
            }
        },
    }
</script>
