<template>
    <div>
        <Repeater id="groups" v-model="groups" :default="newGroup" :validations="$v.groups">
            <template #item="{ id, onUpdate, validations, value }">
                <GroupInfo :id="id" :value="value" :validations="validations" @update:value="onUpdate"/>
            </template>
            <template #addItem="{ action }">
                <button @click="action">Adicionar grupo</button>
            </template>
        </Repeater>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    import GroupInfo from '../components/GroupInfo.vue'
    import Repeater from '../components/Repeater.vue'
    import StepsIndicator from '../components/StepsIndicator.vue'

    export default {
        name: 'ConfirmAppointmentPage',
        components: {
            GroupInfo,
            Icon: FontAwesomeIcon,
            Repeater,
            StepsIndicator,
        },
        data () {
            return {
                groups: []
            }
        },
        validations: {
            groups: {
                $each: {},
            },
        },
        methods: {
            newGroup () {
                return {
                    disabilities: [],
                    languages: [],
                    name: '',
                    num_people: 0,
                    num_responsible: 0,
                    scholarity: '',
                }
            }
        }
    }
</script>
