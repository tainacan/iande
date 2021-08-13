<template>
    <label class="iande-label"><slot/><span class="iande-label__optional" v-if="side">{{ side }}</span></label>
</template>

<script>
    export default {
        name: 'Label',
        props: {
            for: { type: String, required: true },
            side: { type: String, default: '' },
        },
    }
</script>
