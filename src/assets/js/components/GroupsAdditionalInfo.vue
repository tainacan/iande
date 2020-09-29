<template>
    <article class="iande-stack stack-lg">
        <h1>Informações do grupo</h1>
        <p>Nesta etapa você deve dar informações sobre o grupo que irá visitar o museu.</p>
        <Repeater id="groups" class="iande-groups" v-model="groups" :factory="newGroup" :resizable="false" :validations="$v.groups">
            <template #item="{ id, onUpdate, validations, value }">
                <GroupAdditionalInfo :key="id" :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
            </template>
        </Repeater>
        <FormError id="groups__error" :validations="$v.groups" v-if="$v.groups.$error"/>
    </article>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { integer, maxValue, minLength, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import FormError from './FormError.vue'
    import GroupAdditionalInfo from './GroupAdditionalInfo.vue'
    import Repeater from './Repeater.vue'

    export default {
        name: 'GroupsAdditionalInfo',
        components: {
            FormError,
            GroupAdditionalInfo,
            Icon: FontAwesomeIcon,
            Repeater,
        },
        computed: {
            exhibition: get('appointments/exhibition'),
            groups: sync('appointments/current@groups'),
        },
        validations () {
            const maxPeople = this.exhibition ? this.exhibition.group_size : 100
            return {
                groups: {
                    minGroups: minLength(1),
                    $each: {
                        disabilities: {
                            $each: {
                                count: { integer, required },
                                other: { },
                                type: { required },
                            }
                        },
                        languages: {
                            $each: {
                                name: { required },
                                other: { },
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