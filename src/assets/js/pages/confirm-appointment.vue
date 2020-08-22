<template>
    <article>
        <StepsIndicator :step="2"/>

        <div class="iande-container narrow iande-stack stack-lg">
            <h1>Informações do grupo</h1>
            <p>Nesta etapa você deve dar informações sobre o grupo que irá visitar o museu.</p>

            <form class="iande-form iande-stack stack-lg">
                <Repeater id="groups" v-model="groups" :factory="newGroup" :validations="$v.groups">
                    <template #item="{ id, onUpdate, validations, value }">
                        <GroupInfo :key="id" :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
                    </template>
                    <template #addItem="{ action }">
                        <button type="button" @click="action">Adicionar grupo</button>
                    </template>
                </Repeater>
            </form>
        </div>
    </article>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
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
