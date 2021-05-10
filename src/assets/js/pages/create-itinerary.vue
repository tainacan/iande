<template>
    <article class="mt-lg">
        <div class="iande-container narrow iande-stack stack-lg">
            <form class="iande-form iande-stack stack-lg" @submit.prevent="createItinerary">
                <h1>{{ __('Criar roteiro virtual', 'iande') }}</h1>

                <div>
                    <label for="name" class="iande-label">{{ __('Nome do roteiro', 'iande') }}</label>
                    <Input id="name" type="text" :placeholder="__('Título do roteiro', 'iande')" v-model="form.name" :validations="$v.form.name"/>
                </div>

                <div>
                    <label for="source" class="iande-label">{{ __('Escolha como você quer montar o roteiro', 'iande') }}</label>
                    <RadioGroup id="source" columns v-model="form.source" :validations="$v.form.source" :options="sourceOptions"/>
                </div>

                <div v-if="form.source === 'exhibition'">
                    <label for="resource" class="iande-label">{{ __('Selecione uma exposição', 'iande') }}</label>
                    <Select id="resource" :placeholder="__('Selecione uma exposição', 'iande')" v-model="form.resource" :validations="$v.form.resource" :options="exhibitionOptions"/>
                </div>
                <div v-else-if="form.source === 'collection'">
                    <label for="resource" class="iande-label">{{ __('Selecione uma coleção', 'iande') }}</label>
                    <Select id="resource" :placeholder="__('Selecione uma coleção', 'iande')" v-model="form.resource" :validations="$v.form.resource" :options="collectionOptions"/>
                </div>

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
    import { required, requiredIf } from 'vuelidate/lib/validators'
    import { get } from 'vuex-pathify'


    import Input from '@components/Input.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import Select from '@components/Select.vue'
    import { __ } from '@plugins/wp-i18n'
    import { api, constant } from '@utils'

    export default {
        name: 'EditItineraryPage',
        components: {
            Input,
            RadioGroup,
            Select,
        },
        data () {
            return {
                collections: [],
                form: {
                    name: '',
                    resource: undefined,
                    source: '',
                },
                formError: '',
            }
        },
        computed: {
            collectionOptions () {
                const entries = this.collections.map(collection => [collection.name, collection.id])
                return Object.fromEntries(entries)
            },
            exhibitions: get('exhibitions/list'),
            exhibitionOptions () {
                const entries = this.exhibitions.map(exhibition => [exhibition.title, exhibition.ID])
                return Object.fromEntries(entries)
            },
            sourceOptions: constant({
                [__('A partir de uma exposição', 'iande')]: 'exhibition',
                [__('A partir de uma coleção específica', 'iande')]: 'collection',
                [__('A partir do repositório completo de itens do museu', 'iande')]: 'all',
            }),
            specificSource () {
                return ['collection', 'exhibition'].includes(this.form.source)
            },
        },
        validations () {
            return {
                form: {
                    name: { required },
                    resource: { required: requiredIf('specificSource') },
                    source: { required },
                },
            }
        },
        async created () {
            try {
                const collections = await api.get(`${this.$iande.siteUrl}/wp-json/tainacan/v2/collections`)
                this.collections = collections
            } catch (err) {
                this.formError = err
            }
        },
        methods: {
            createItinerary () {
                this.formError = ''
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {

                    } catch (err) {
                        this.formError = err
                    }
                }
            }
        }
    }
</script>
