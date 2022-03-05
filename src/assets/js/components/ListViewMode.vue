<template>
    <div class="iande-container">
        <div class="iande-tainacan iande-tainacan-list">
            <table>
                <thead>
                    <tr>
                        <th :aria-label="__('Controles', 'iande')"/>
                        <th>{{ __('Miniatura', 'iande') }}</th>
                        <th>{{ __('Título', 'iande') }}</th>
                        <th>{{ __('Descrição', 'iande') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item of items" :key="item.id">
                        <td class="iande-tainacan-list__controls iande-tainacan-table__controls">
                            <button type="button" class="iande-button iande-tainacan-check-button selected" :aria-label="__('Remover', 'iande')" @click="removeItem(item)" v-if="isChecked(item)">
                                <Icon icon="circle-check"/>
                            </button>
                            <button type="button" class="iande-button iande-tainacan-check-button" :aria-label="__('Adicionar', 'iande')" @click="addItem(item)" v-else>
                                <Icon icon="circle-plus"/>
                            </button>
                        </td>
                        <td>
                            <img :src="item.thumbnail.thumbnail[0]" :alt="item.thumbnail_alt" height="64" width="64" @click="seeDetails(item)">
                        </td>
                        <td v-html="getMeta(item, 'title')"/>
                        <td v-html="getMeta(item, 'description')"/>
                    </tr>
                </tbody>
            </table>
        </div>
        <ViewModeModal ref="modal" :item="selectedItem" v-if="selectedItem"/>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import ViewModeModal from '@components/ViewModeModal.vue'
    import ViewMode from '@mixins/ViewMode'

    export default {
        name: 'IandeListViewMode',
        components: {
            Icon: FontAwesomeIcon,
            ViewModeModal,
        },
        mixins: [ViewMode],
        methods: {
            thumbnail (item) {
                return item.thumbnail.thumbnail
            },
        },
    }
</script>
