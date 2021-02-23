<template>
    <div class="iande-container">
        <pre>{{ this.$props }}</pre>
    </div>
</template>

<script>
    export default {
        name: 'IandeViewMode',
        props: {
            collectionId: Number,
            displayedMetadata: Array,
            items:  {
                type: Array,
                default: () => [],
            },
            isLoading: false,
            totalItems: Number,
            isFiltersMenuCompressed: Boolean,
            enabledViewModes: Array
        },
    }
</script>
