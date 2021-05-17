<template>
    <div>
        <component :key="page" :is="component" v-if="component"/>
    </div>
</template>

<script>
    export default {
        name: 'PageLoader',
        props: {
            page: { type: String, required: true },
        },
        data () {
            return {
                component: null,
            }
        },
        async created () {
            try {
                const module = await import(/* webpackMode: "lazy", webpackChunkName: "[request]-page" */ `@pages/${this.page}`)
                this.component = module.default
            } catch (err) {
                console.error(err)
            }
        },
    }
</script>
