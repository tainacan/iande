<template>
    <article class="iande-stack stack-lg">
        <h1>{{ __('Reserve dia e horário', 'iande') }}</h1>
        <Repeater id="groups" class="iande-groups" v-model="groups" :factory="newGroup" :resizable="false" :v="$v.groups">
            <template #item="{ id, onUpdate, v, value }">
                <GroupDate :key="id" :id="id" :value="value" :v="v" @updateValue="onUpdate"/>
            </template>
        </Repeater>
        <FormError id="groups__error" :v="$v.groups" v-if="$v.groups.$error"/>
    </article>
</template>

<script>
    import { minLength, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import FormError from '@components/FormError.vue'
    import GroupDate from '@components/GroupDate.vue'
    import Repeater from '@components/Repeater.vue'
    import { date, time } from '@utils/validators'

    export default {
        name: 'GroupsDate',
        components: {
            FormError,
            GroupDate,
            Repeater,
        },
        computed: {
            exhibition: get('appointments/exhibition'),
            groups: sync('appointments/current@groups'),
            numPeople: get('appointments/current@num_people'),
        },
        validations: {
            groups: {
                minGroups:minLength(1),
                $each: {
                    date: { required, date },
                    hour: { required, time },
                    name: { required },
                },
            },
        },
        created () {
            if (this.groups.length === 0) {
                const groupSize = this.exhibition?.group_size ? Number(this.exhibition.group_size) : 100
                const numGroups = Math.ceil(this.numPeople / groupSize)
                this.groups = [...new Array(numGroups)].map(this.newGroup)
            }
        },
        methods: {
            newGroup () {
                return {
                    age_range: '',
                    date: null,
                    disabilities: [],
                    hour: null,
                    languages: [],
                    name: '',
                    num_people: 5,
                    num_responsible: 1,
                    scholarity: '',
                }
            }
        }
    }
</script>
