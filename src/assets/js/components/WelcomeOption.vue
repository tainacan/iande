<template>
    <section class="iande-welcome-option">
        <a class="iande-button iande-welcome-option__title" :href="href">
            <span>{{ title }}</span>
            <Icon icon="angle-right"/>
        </a>
        <div class="iande-welcome-option__description">
            <slot/>
        </div>
    </section>
</template>

<script>
    export default {
        name: 'WelcomeOption',
        props: {
            href: { type: String, required: true },
            title: { type: String, required: true },
        },
    }
</script>