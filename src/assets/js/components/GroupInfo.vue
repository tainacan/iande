<template>
    <div class="iande-stack stack-lg">
        <div>
            <label :for="`${id}_name`" class="iande-label">Nome do grupo</label>
            <Input :id="`${id}_name`" type="text" placeholder="Ex.: 1° ano G - Prof. Marta" v-model="name" :validations="validations.name"/>
        </div>
        <div>
            <label :for="`${id}_numPeople`" class="iande-label">Quantidade prevista de pessoas</label>
            <Input :id="`${id}_numPeople`" type="number" min="5" placeholder="Mínimo de 5 pessoas" v-model.number="numPeople" :validations="validations.num_people"/>
            <p class="text-sm">Caso seu grupo seja maior do que a capacidade de atendimento do museu, adicione outro grupo no fim da página.</p>
        </div>
        <div>
            <label :for="`${id}_scholarity`" class="iande-label">Escolaridade</label>
            <Select :id="`${id}_scholarity`" v-model="scholarity" :validations="validations.scholarity" :options="scholarityOptions"/>
        </div>
        <div>
            <label :for="`${id}_numResponsible`" class="iande-label">Quantidade prevista de responsáveis</label>
            <Input :id="`${id}_numResponsible`" type="number" min="1" max="2" placeholder="Mínimo de 1 e máximo de 2 pessoas" v-model.number="numResponsible" :validations="validations.num_responsible"/>
        </div>
        <div>
            <label :for="`${id}_otherLanguages`" class="iande-label">O grupo fala algum idioma diferente de português?</label>
            <RadioGroup :id="`${id}_otherLanguages`" v-model="otherLanguages" :validations="$v.otherLanguages" :options="binaryOptions"/>
        </div>
        <div v-if="otherLanguages">
            <Repeater :id="`${id}_languages`" v-model="languages" :factory="newLanguage" :validations="validations.languages">
                <template #item="{ id, onUpdate, validations, value }">
                    <Select :id="id" :value="value" :validations="validations" :options="languageOptions" @updateValue="onUpdate"/>
                </template>
                <template #addItem="{ action }">
                    <div class="iande-add-item" role="button" tabindex="0" @click="action">
                        <span><Icon icon="plus-circle"/></span>
                        <div class="iande-label">Adicionar outro idioma</div>
                    </div>
                </template>
            </Repeater>
        </div>
    </div>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { required } from 'vuelidate/lib/validators'

    import Input from './Input.vue'
    import RadioGroup from './RadioGroup.vue'
    import Repeater from './Repeater.vue'
    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'
    import { constant, subModel } from '../utils'

    export default {
        name: 'GroupInfo',
        components: {
            Icon: FontAwesomeIcon,
            Input,
            RadioGroup,
            Repeater,
            Select,
        },
        mixins: [CustomField],
        data () {
            return {
                otherLanguages: false,
            }
        },
        computed: {
            binaryOptions: constant({ 'Sim': true, 'Não': false }),
            languages: subModel('languages'),
            languageOptions: constant(window.IandeSettings.languages),
            name: subModel('name'),
            numPeople: subModel('num_people'),
            numResponsible: subModel('num_responsible'),
            scholarity: subModel('scholarity'),
            scholarityOptions: constant(window.IandeSettings.scholarity),
        },
        validations: {
            otherLanguages: { },
        },
        watch: {
            languages: {
                handler () {
                    if (this.languages.length > 0) {
                        this.otherLanguages = true
                    }
                },
                immediate: true
            },
            otherLanguages () {
                if (!this.otherLanguages) {
                    this.languages = []
                }
            }
        },
        methods: {
            newLanguage () {
                return ''
            }
        }
    }
</script>