<template>
    <tr>
        <td class="iande-itinerary-table__controls iande-tainacan-table__controls">
            <div role="button" tabindex="0" :aria-role="__('Remover', 'iande')" @click="$emit('remove', item.items_id)">
                <Icon :icon="['far', 'trash-can']"/>
            </div>
            <div class="-handle" aria-hidden="true">
                <Icon icon="grip-vertical"/>
            </div>
        </td>
        <td>
            <img :src="thumbnail" alt="" height="64" width="64" @click="$emit('details', meta)">
        </td>
        <td>{{ meta.title }}</td>
        <td>
            <textarea :placeholder="meta.description" v-model="item.items_description"/>
        </td>
    </tr>
</template>

<script>
    export default {
        name: 'ItineraryItemsRow',
        props: {
            item: { type: Object, required: true },
            meta: { type: Object, required: true },
        },
        computed: {
            thumbnail () {
                if (!this.meta.attachments) {
                    return `${this.$iande.iandePath}assets/img/cover-placeholder.png`
                }
                return this.meta.attachments?.[0]?.thumbnails.thumbnail[0] || `${this.$iande.iandePath}assets/img/cover-placeholder.png`
            }
        },
    }
</script>
