<template>
    <div id="iande-confirm-itinerary">
        <div/>
        <article>
            <aside class="iande-sidebar" :class="{ '-collapsed': menuCollapsed }">
                <div>
                    <button id="iande-collapse-button" type="button" class="iande-button small solid" @click="menuCollapsed = !menuCollapsed">
                        <Icon :icon="menuCollapsed ? 'angle-double-right' : 'angle-double-left'"/>
                        <span>{{ menuCollapsed ? __('Exibir menu', 'iande') : __('Ocultar menu', 'iande') }}</span>
                    </button>
                    <button type="button" class="iande-button small" :class="view === 'settings' ? 'secondary' : 'solid'" @click="view = 'settings'">
                        <Icon icon="cog"/>
                        <span>{{ __('Configurações do roteiro', 'iande') }}</span>
                    </button>
                    <button type="button" class="iande-button small" :class="view === 'items' ? 'secondary' : 'solid'" @click="view = 'items'">
                        <Icon icon="check-circle"/>
                        <span>{{ __('Itens selecionados', 'iande') }}</span>
                    </button>
                </div>
                <div>
                    <button type="button" class="iande-button small solid" @click="update">
                        <Icon :icon="['far', 'save']"/>
                        <span>{{ __('Salvar rascunho', 'iande') }}</span>
                    </button>
                    <button type="button" class="iande-button small solid" @click="remove">
                        <Icon :icon="['far', 'trash-alt']"/>
                        <span>{{ __('Apagar roteiro', 'iande') }}</span>
                    </button>
                    <button type="button" class="iande-button small primary">
                        <Icon icon="check"/>
                        <span>{{ __('Publicar roteiro', 'iande') }}</span>
                    </button>
                    <a :href="$iandeUrl('itinerary/list')">
                        <Icon icon="angle-left"/>
                        <span>{{ __('Voltar à lista de roteiros', 'iande') }}</span>
                    </a>
                </div>
            </aside>
            <main>
                <div class="iande-container narrow mt-lg">
                    <form class="iande-form iande-stack stack-lg" @submit.prevent v-if="itinerary">
                        <h1>{{ __('Finalizando roteiro', 'iande') }}</h1>

                        <div>
                            <label for="name" class="iande-label">{{ __('Título do roteiro', 'iande') }}</label>
                            <Input id="name" type="text" :placeholder="__('Título do roteiro', 'iande')" v-model="itinerary.name" :validations="$v.itinerary.name"/>
                        </div>

                        <div>
                            <label for="cover" class="iande-label">{{ __('Imagem de capa', 'iande') }}</label>
                            <FileUploader id="cover" accept="image/*" v-model="itinerary.cover" :validations="$v.itinerary.cover"/>
                        </div>

                        <div>
                            <label for="description" class="iande-label">{{ __('Descrição breve', 'iande') }}<span class="iande-label__optional">{{ __('(opcional)', 'iande') }}</span></label>
                            <TextArea id="description" v-model="itinerary.description" :validations="$v.itinerary.description"/>
                        </div>

                        <div>
                            <label for="publicly_findable" class="iande-label">{{ __('O roteiro será aberto ao público ou restrito a quem tiver o link de acesso?', 'iande') }}</label>
                            <RadioGroup id="publicly_findable" v-model="itinerary.publicly_findable" :options="publicOptions" :validations="$v.itinerary.publicly_findable"/>
                        </div>

                        <div>
                            <label for="shareable" class="iande-label">{{ __('Você deseja deixar seu roteiro compartilhável?', 'iande') }}</label>
                            <RadioGroup id="shareable" v-model="itinerary.shareable" :options="shareOptions" :validations="$v.itinerary.shareable"/>
                        </div>

                        <div>
                            <label for="layout" class="iande-label">{{ __('Escolha o layout de visualização desktop do roteiro', 'iande') }}</label>
                            <LayoutSelector id="layout" v-model="itinerary.layout" :validations="$v.itinerary.layout"/>
                        </div>

                        <div class="iande-form-grid">
                            <a class="iande-button solid" :href="$iandeUrl('itinerary/list')">
                                {{ __('Cancelar', 'iande') }}
                            </a>

                            <button type="button" class="iande-button primary">
                                {{ __('Salvar alterações', 'iande') }}
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </article>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import FileUploader from '@components/FileUploader.vue'
    import Input from '@components/Input.vue'
    import LayoutSelector from '@components/LayoutSelector.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import TextArea from '@components/TextArea.vue'
    import { __ } from '@plugins/wp-i18n'
    import { api, constant, qs } from '@utils'

    export default {
        name: 'ConfirmItineraryPage',
        components: {
            FileUploader,
            Input,
            LayoutSelector,
            RadioGroup,
            TextArea,
        },
        data () {
            return {
                formError: '',
                items: [],
                itinerary: {
                    items: [],
                },
                menuCollapsed: true,
                view: 'settings',
            }
        },
        computed: {
            publicOptions: constant({
                [__('Aberto ao público', 'iande')]: 'yes',
                [__('Acesso apenas com link', 'iande')]: 'no',
            }),
            shareOptions: constant({
                [__('Não', 'iande')]: 'no',
                [__('Sim', 'iande')]: 'yes',
            }),
        },
        validations: {
            itinerary: {
                cover: { },
                description: { },
                layout: { required },
                name: { required },
                publicly_findable: { required },
                shareable: { required },
            },
        },
        async beforeMount () {
            if (qs.has('ID')) {
                try {
                    const [items, itinerary] = await Promise.all([
                        api.get(`${this.$iande.tainacanUrl}/items/?perpage=10000`),
                        api.get('itinerary/get', { ID: Number(qs.get('ID')) }),
                    ])
                    if (!itinerary.items) {
                        itinerary.items = []
                    }
                    this.items = items.items
                    this.itinerary = itinerary
                } catch (err) {
                    this.formError = err
                }
            }
        },
        methods: {
            async remove () {
                try {
                    await api.post('itinerary/delete', { ID: this.itinerary.ID })
                    window.location.assign(this.$iandeUrl('itinerary/list'))
                } catch (err) {
                    this.formError = err
                }
            },
            async update () {
                try {
                    await api.post('itinerary/update', this.itinerary)
                    window.location.assign(this.$iandeUrl('itinerary/list'))
                } catch (err) {
                    this.formError = err
                }
            },
        },
    }
</script>