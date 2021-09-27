<template>
    <tr>
        <td v-if="exhibitions.length > 1">{{ exhibition.title }}</td>
        <td>{{ formatDate(group.date) }} {{ group.hour }}</td>
        <td v-if="appointment">{{ appointment.ID }} - {{ appointment.name }}</td>
        <td v-else>{{ __('N/A', 'iande') }}</td>
        <td>
            <div>{{ group.ID }} - {{ group.name }}</div>
            <div>{{ sprintf(__('%s pessoas', 'iande'), group.num_people) }}</div>
            <div>{{ group.age_range }}</div>
        </td>
        <td v-if="institution">
            <div>{{ institution.name }}</div>
            <div v-if="institution.phone">{{ formatPhone(institution.phone) }}</div>
            <div v-if="institution.email">{{ institution.email }}</div>
        </td>
        <td v-else>{{ __('N/A', 'iande') }}</td>
        <td v-if="appointment">
            <div>{{ appointment.responsible_first_name }}</div>
            <div>{{ formatPhone(appointment.responsible_phone) }}</div>
            <div>{{ appointment.responsible_email }}</div>
        </td>
        <td v-else>{{ __('N/A', 'iande') }}</td>
        <td v-if="educator">{{ educator.display_name }}</td>
        <td class="fillable" v-else></td>
    </tr>
</template>

<script>
    import { DateTime } from 'luxon'

    import { formatPhone } from '@utils/'

    export default {
        name: 'PrintGroupsRow',
        props: {
            appointments: { type: Object, required: true },
            educators: { type: Object, required: true },
            exhibitions: { type: Array, required: true },
            group: { type: Object, required: true },
            institutions: { type: Object, required: true },
        },
        computed: {
            appointment () {
                return this.appointments[this.group.appointment_id]
            },
            educator () {
                return this.educators[this.group.educator_id]
            },
            exhibition () {
                return this.exhibitions.find(exhibition => exhibition.ID == this.group.exhibition_id)
            },
            institution () {
                if (!this.appointment) {
                    return null
                }
                return this.institutions[this.appointment.institution_id]
            },
        },
        methods: {
            formatDate (date) {
                return DateTime.fromISO(date).toLocaleString(DateTime.DATE_SHORT)
            },
            formatPhone,
        },
    }
</script>
