<template>
    <tr>
        <td class="iande-itinerary-table__controls iande-tainacan-table__controls">
            <div role="button" tabindex="0" :aria-role="__('Remover', 'iande')" @click="$emit('remove', item.items_id)">
                <Icon :icon="['far', 'trash-alt']"/>
            </div>
            <div class="-handle" aria-hidden="true">
                <Icon icon="grip-vertical"/>
            </div>
        </td>
        <td>
            <img :src="thumbnail" alt="" height="64" width="64" v-if="thumbnail">
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
                    return null
                }
                return this.meta.attachments?.[0]?.thumbnails.thumbnail[0]
            }
        },
    }
</script>
