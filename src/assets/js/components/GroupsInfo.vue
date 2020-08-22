<template>
    <div class="iande-stack stack-lg">
        <h1>Informações do grupo</h1>
        <p>Nesta etapa você deve dar informações sobre o grupo que irá visitar o museu.</p>
        <Repeater id="groups" class="iande-groups" v-model="groups" :factory="newGroup" :validations="$v.groups">
            <template #item="{ id, onUpdate, validations, value }">
                <GroupInfo :key="id" :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
            </template>
            <template #addItem="{ action }">
                <button class="iande-add-group iande-button" type="button" @click="action">
                    <span><Icon icon="plus-circle"/></span>
                    Adicionar grupo
                </button>
            </template>
        </Repeater>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import GroupInfo from './GroupInfo.vue'
    import Repeater from './Repeater.vue'

    export default {
        name: 'GroupsInfo',
        components: {
            GroupInfo,
            Icon: FontAwesomeIcon,
            Repeater,
        },
        computed: {
            groups: sync('appointments/current@group_list.groups'),
        },
        validations: {
            groups: {
                $each: {
                    disabilities: {
                        $each: {
                            count: { required },
                            type: { required },
                        }
                    },
                    languages: {
                        $each: { required },
                    },
                    name: { required },
                    num_people: { required },
                    num_responsible: { required },
                    scholarity: { required },
                },
            },
        },
        methods: {
            newGroup () {
                return {
                    disabilities: [],
                    id: this.groups.length + 1,
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
