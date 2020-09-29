<template>
    <section class="iande-group iande-stack stack-lg">
        <h2 class="iande-group-title">Grupo {{ n }}:</h2>
        <div>
            <label :for="`${id}_numPeople`" class="iande-label">Quantidade prevista de pessoas<span class="iande-label__optional">Máximo de {{ maxPeople }} pessoas</span></label>
            <Input :id="`${id}_numPeople`" type="number" min="5" :max="maxPeople" placeholder="Mínimo de 5 pessoas" v-model.number="numPeople" :validations="validations.numPeople"/>
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
        <template v-if="otherLanguages">
            <Repeater :id="`${id}_languages`" v-model="languages" :factory="newLanguage" :validations="validations.languages">
                <template #item="{ id, onUpdate, validations, value }">
                    <div :key="id">
                        <LanguageInfo :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
                    </div>
                </template>
                <template #addItem="{ action }">
                    <div class="iande-add-item" role="button" tabindex="0" @click="action">
                        <span><Icon icon="plus-circle"/></span>
                        <div class="iande-label">Adicionar outro idioma</div>
                    </div>
                </template>
            </Repeater>
        </template>
        <div>
            <label :for="`${id}_haveDisabilities`" class="iande-label">Há pessoa com deficiência no grupo?</label>
            <RadioGroup :id="`${id}_haveDisabilities`" v-model="haveDisabilities" :validations="$v.haveDisabilities" :options="binaryOptions"/>
        </div>
        <template v-if="haveDisabilities">
            <Repeater :id="`${id}_disabilities`" v-model="disabilities" :factory="newDisability" :validations="validations.disabilities">
                <template #item="{ id, onUpdate, validations, value }">
                    <div :key="id">
                        <DisabilityInfo :id="id" :value="value" :validations="validations" @updateValue="onUpdate"/>
                    </div>
                </template>
                <template #addItem="{ action }">
                    <div class="iande-add-item" role="button" tabindex="0" @click="action">
                        <span><Icon icon="plus-circle"/></span>
                        <div class="iande-label">Adicionar tipo de deficiência</div>
                    </div>
                </template>
            </Repeater>
        </template>
    </section>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { get } from 'vuex-pathify'

    import DisabilityInfo from './DisabilityInfo.vue'
    import Input from './Input.vue'
    import LanguageInfo from './LanguageInfo.vue'
    import RadioGroup from './RadioGroup.vue'
    import Repeater from './Repeater.vue'
    import Select from './Select.vue'
    import CustomField from './mixins/CustomField'
    import { constant, subModel } from '../utils'

    export default {
        name: 'GroupAdditionalInfo',
        components: {
            DisabilityInfo,
            Icon: FontAwesomeIcon,
            Input,
            LanguageInfo,
            RadioGroup,
            Repeater,
            Select,
        },
        mixins: [CustomField],
        data () {
            return {
                haveDisabilities: false,
                otherLanguages: false,
            }
        },
        computed: {
            binaryOptions: constant({ 'Não': false, 'Sim': true }),
            disabilities: subModel('disabilities'),
            exhibition: get('appointments/exhibition'),
            languages: subModel('languages'),
            maxPeople () {
                return this.exhibition ? this.exhibition.group_size : 100
            },
            n () {
                return Number(this.id.split('_').pop()) + 1
            },
            name: subModel('name'),
            numPeople: subModel('num_people'),
            numResponsible: subModel('num_responsible'),
            scholarity: subModel('scholarity'),
            scholarityOptions: constant(window.IandeSettings.scholarity),
        },
        validations: {
            haveDisabilities: { },
            otherLanguages: { },
        },
        watch: {
            disabilities: {
                handler () {
                    if (this.disabilities.length > 0) {
                        this.haveDisabilities = true
                    }
                },
                immediate: true
            },
            languages: {
                handler () {
                    if (this.languages.length > 0) {
                        this.otherLanguages = true
                    }
                },
                immediate: true
            },
            haveDisabilities () {
                if (!this.haveDisabilities) {
                    this.disabilities = []
                }
            },
            otherLanguages () {
                if (!this.otherLanguages) {
                    this.languages = []
                }
            }
        },
        methods: {
            newDisability () {
                return {
                    type: '',
                    other: '',
                    count: 1,
                }
            },
            newLanguage () {
                return {
                    name: '',
                    other: '',
                }
            }
        }
    }
</script>