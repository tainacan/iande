<template>
    <article class="iande-stack stack-lg">
        <h1>{{ __('Informações do grupo', 'iande') }}</h1>
        <p>{{ __('Nesta etapa você deve dar informações sobre o grupo que irá visitar o museu.', 'iande') }}</p>
        <Repeater id="groups" class="iande-groups" v-model="groups" :factory="newGroup" :resizable="false" :v="$v.groups">
            <template #item="{ id, onUpdate, v, value }">
                <GroupAdditionalInfo :key="id" :id="id" :value="value" :v="v" @updateValue="onUpdate"/>
            </template>
        </Repeater>
        <FormError id="groups__error" :v="$v.groups" v-if="$v.groups.$error"/>
    </article>
</template>

<script>
    import { integer, maxValue, minLength, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import FormError from '@components/FormError.vue'
    import GroupAdditionalInfo from '@components/GroupAdditionalInfo.vue'
    import Repeater from '@components/Repeater.vue'

    export default {
        name: 'GroupsAdditionalInfo',
        components: {
            FormError,
            GroupAdditionalInfo,
            Repeater,
        },
        computed: {
            exhibition: get('appointments/exhibition'),
            groups: sync('appointments/current@groups'),
        },
        validations () {
            const maxPeople = this.exhibition?.group_size ? Number(this.exhibition.group_size) : 100
            return {
                groups: {
                    minGroups: minLength(1),
                    $each: {
                        age_range: { required },
                        disabilities: {
                            $each: {
                                disabilities_count: { integer, required },
                                disabilities_other: { },
                                disabilities_type: { required },
                            }
                        },
                        languages: {
                            $each: {
                                languages_name: { required },
                                languages_other: { },
                            },
                        },
                        num_people: { integer, maxValue: maxValue(maxPeople), required },
                        num_responsible: { integer, required },
                        scholarity: { required },
                    },
                },
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
