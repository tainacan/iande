<template>
    <section class="iande-group iande-stack stack-lg">
        <h2 class="iande-group-title">{{ sprintf(__('Grupo %s: %s', 'iande'), n, value.name) }}</h2>
        <div>
            <label :for="`${id}_numPeople`" class="iande-label">{{ __('Quantidade prevista de pessoas', 'iande') }}<span class="iande-label__optional">{{ sprintf(__('Máximo de %s pessoas', 'iande'), maxPeople) }}</span></label>
            <Input :id="`${id}_numPeople`" type="number" :min="minPeople" :max="maxPeople" :placeholder="sprintf(__('Mínimo de %s pessoas', 'iande'), minPeople)" v-model.number="numPeople" :validations="validations.num_people"/>
        </div>
        <div>
            <label :for="`${id}_ageRange`" class="iande-label">{{ __('Perfil etário', 'iande') }}</label>
            <Select :id="`${id}_ageRange`" v-model="ageRange" :validations="validations.age_range" :options="$iande.ageRanges"/>
        </div>
        <div>
            <label :for="`${id}_scholarity`" class="iande-label">{{ __('Escolaridade', 'iande') }}</label>
            <Select :id="`${id}_scholarity`" v-model="scholarity" :validations="validations.scholarity" :options="$iande.scholarity"/>
        </div>
        <div>
            <label :for="`${id}_numResponsible`" class="iande-label">{{ __('Quantidade prevista de responsáveis', 'iande') }}</label>
            <Input :id="`${id}_numResponsible`" type="number" min="1" max="2" :placeholder="__('Mínimo de 1 e máximo de 2 pessoas', 'iande')" v-model.number="numResponsible" :validations="validations.num_responsible"/>
        </div>
        <div>
            <label :for="`${id}_otherLanguages`" class="iande-label">{{ __('O grupo fala algum idioma diferente de português?', 'iande') }}</label>
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
                        <div class="iande-label">{{ __('Adicionar idioma', 'iande') }}</div>
                    </div>
                </template>
            </Repeater>
        </template>
        <div>
            <label :for="`${id}_haveDisabilities`" class="iande-label">{{ __('Há pessoa com necessidade especial no grupo?', 'iande') }}</label>
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
                        <div class="iande-label">{{ __('Adicionar necessidade especial', 'iande') }}</div>
                    </div>
                </template>
            </Repeater>
        </template>
    </section>
</template>

<script>
    import { get } from 'vuex-pathify'

    import DisabilityInfo from '@components/DisabilityInfo.vue'
    import Input from '@components/Input.vue'
    import LanguageInfo from '@components/LanguageInfo.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import Repeater from '@components/Repeater.vue'
    import Select from '@components/Select.vue'
    import CustomField from '@mixins/CustomField'
    import { constant, subModel } from '@utils'

    export default {
        name: 'GroupAdditionalInfo',
        components: {
            DisabilityInfo,
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
            ageRange: subModel('age_range'),
            binaryOptions: constant({ 'Não': false, 'Sim': true }),
            disabilities: subModel('disabilities'),
            exhibition: get('appointments/exhibition'),
            languages: subModel('languages'),
            maxPeople () {
                return this.exhibition?.group_size ? Number(this.exhibition.group_size) : 100
            },
            minPeople () {
                return this.exhibition?.min_group_size ? Number(this.exhibition.mingroup_size) : 5
            },
            n () {
                return Number(this.id.split('_').pop()) + 1
            },
            name: subModel('name'),
            numPeople: subModel('num_people'),
            numResponsible: subModel('num_responsible'),
            scholarity: subModel('scholarity'),
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
                    disabilities_type: '',
                    disabilities_other: '',
                    disabilities_count: 1,
                }
            },
            newLanguage () {
                return {
                    languages_name: '',
                    languages_other: '',
                }
            }
        }
    }
</script>