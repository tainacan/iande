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
                <div v-if="itinerary">
                    <ItineraryDetails :formError="formError" :itinerary="itinerary" :v="$v" @update="update" v-if="view === 'settings'"/>
                    <ItineraryItems :formError="formError" :items="items" :itinerary="itinerary" :v="$v" @update="update" v-else-if="view === 'items'"/>
                </div>
            </main>
        </article>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'

    import ItineraryDetails from '@components/ItineraryDetails.vue'
    import ItineraryItems from '@components/ItineraryItems.vue'
    import { api, qs } from '@utils'

    export default {
        name: 'ConfirmItineraryPage',
        components: {
            ItineraryDetails,
            ItineraryItems,
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
                    this.formError = ''
                    await api.post('itinerary/delete', { ID: this.itinerary.ID })
                    window.location.assign(this.$iandeUrl('itinerary/list'))
                } catch (err) {
                    this.formError = err
                }
            },
            async update () {
                try {
                    this.formError = ''
                    await api.post('itinerary/update', this.itinerary)
                    window.location.assign(this.$iandeUrl('itinerary/list'))
                } catch (err) {
                    this.formError = err
                }
            },
        },
    }
</script>
