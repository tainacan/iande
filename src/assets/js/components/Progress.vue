<template>
    <div class="iande-progress" :aria-valuemax="max" :aria-valuemin="min" :aria-valuenow="value" role="progressbar" :title="title">
        <div v-for="n of (max - min)" :key="n" :class="{ active: (n <= value - min) }"></div>
    </div>
</template>

<script>
    export default {
        name: 'Progress',
        props: {
            max: { type: Number, required: true },
            min: { type: Number, default: 0 },
            title: { type: String, required: true },
            value: { type: Number, required: true },
        },
        computed: {
            percentage () {
                return `${100 * (this.value - this.min) / (this.max - this.min)}%`
            }
        }
    }
</script>
