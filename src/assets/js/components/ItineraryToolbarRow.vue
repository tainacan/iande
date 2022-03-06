<template>
    <tr>
        <td class="iande-itinerary-table__controls iande-tainacan-table__controls">
            <div role="button" tabindex="0" :aria-role="__('Remover', 'iande')" @click="$emit('remove', item)">
                <Icon :icon="['far', 'trash-can']"/>
            </div>
            <div class="-handle" aria-hidden="true">
                <Icon icon="grip-vertical"/>
            </div>
        </td>
        <td>
            <img :src="thumbnail" alt="" height="64" width="64" @click="$emit('details', item)">
        </td>
        <td>{{ item.title }}</td>
        <td>{{ item.description }}</td>
    </tr>
</template>

<script>
    export default {
        name: 'ItineraryToolbarRow',
        props: {
            item: { type: Object, required: true },
        },
        computed: {
            thumbnail () {
                if (!this.item.attachments) {
                    return null || `${this.$iande.iandePath}assets/img/cover-placeholder.png`
                }
                return this.item.attachments?.[0]?.thumbnails.thumbnail[0] || `${this.$iande.iandePath}assets/img/cover-placeholder.png`
            }
        },
    }
</script>