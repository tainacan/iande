<template>
    <div>
        <component :key="page" :is="component" v-if="component" v-bind="props"/>
    </div>
</template>

<script>
    export default {
        name: 'Page',
        props: {
            page: { type: String, required: true },
            props: { type: Object, default: () => ({}) },
        },
        data () {
            return {
                component: null,
            }
        },
        async created () {
            this.component = (await import(/* webpackMode: "lazy", webpackChunkName: "[request]-page" */ `@pages/${this.page}`)).default
        }
    }
</script>
