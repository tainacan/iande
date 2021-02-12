<template>
    <section class="iande-welcome-option">
        <button type="button" class="iande-button iande-welcome-option__title" @click="$emit('select')">
            <span>{{ title }}</span>
            <Icon icon="angle-right"/>
        </button>
        <div class="iande-welcome-option__description">
            <slot/>
        </div>
    </section>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    export default {
        name: 'WelcomeOption',
        components: {
            Icon: FontAwesomeIcon,
        },
        props: {
            title: { type: String, required: true },
        },
    }
</script>