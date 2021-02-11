<template>
    <section class="iande-welcome-option">
        <h2 class="iande-welcome-option__title">
            <a :href="href">
                <span>{{ title }}</span>
                <Icon icon="angle-right"/>
            </a>
        </h2>
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
            href: { type: String, required: true },
            title: { type: String, required: true },
        },
    }
</script>