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
                    <button type="button" class="iande-button small solid">
                        <Icon :icon="['far', 'save']"/>
                        <span>{{ __('Salvar rascunho', 'iande') }}</span>
                    </button>
                    <button type="button" class="iande-button small solid">
                        <Icon :icon="['far', 'trash-alt']"/>
                        <span>{{ __('Apagar roteiro', 'iande') }}</span>
                    </button>
                    <button type="button" class="iande-button small primary">
                        <Icon icon="check"/>
                        <span>{{ __('Publicar roteiro', 'iande') }}</span>
                    </button>
                    <a href="#">
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
                            <label for="description" class="iande-label">{{ __('Descrição breve', 'iande') }}</label>
                            <TextArea id="description" v-model="itinerary.description" :validations="$v.itinerary.description"/>
                        </div>
                    </form>
                </div>
            </main>
        </article>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import Input from '@components/Input.vue'
    import TextArea from '@components/TextArea.vue'
    import { api, qs } from '@utils'

    export default {
        name: 'ConfirmItineraryPage',
        components: {
            Input,
            TextArea,
        },
        data () {
            return {
                items: [],
                itinerary: {
                    items: [],
                },
                menuCollapsed: true,
                view: 'settings',
            }
        },
        validations: {
            itinerary: {
                description: { required },
                name: { required },
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
                    console.error(err)
                }
            }
        },
    }
</script>