<template>
    <article class="iande-stack stack-lg">
        <h1>Reserve dia e horário</h1>
        <Repeater id="groups" class="iande-groups" v-model="groups" :factory="newGroup" :resizable="false" :validations="$v.groups">
            <template #item="{ id, onUpdate, validations, value }">
                <GroupDate :key="id" :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
            </template>
        </Repeater>
        <FormError id="groups__error" :validations="$v.groups" v-if="$v.groups.$error"/>
    </article>
</template>

<script>
    import { minLength, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import FormError from './FormError.vue'
    import GroupDate from './GroupDate.vue'
    import Repeater from './Repeater.vue'
    import { date, time } from '../utils/validators'

    export default {
        name: 'GroupsDate',
        components: {
            FormError,
            GroupDate,
            Repeater,
        },
        computed: {
            groups: sync('appointments/current@group_list.groups'),
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
        methods: {
            newGroup () {
                return {
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