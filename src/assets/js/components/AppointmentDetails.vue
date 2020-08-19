<template>
    <pre>
        {{ appointment }}
    </pre>
</template>

<script>
    export default {
        name: 'AppointmentDetails',
        props: {
            appointment: { type: Object, required: true },
        }
    }
</script>