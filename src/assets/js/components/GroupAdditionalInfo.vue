<template>
    <section class="iande-group iande-stack stack-lg">
        <h2 class="iande-group-title">{{ sprintf(__('Grupo %s: %s', 'iande'), n, value.name) }}</h2>
        <div>
            <Label :for="`${id}_numPeople`" :side="sprintf(__('Máximo de %s pessoas', 'iande'), maxPeople)">{{ __('Quantidade prevista de pessoas', 'iande') }}</Label>
            <Input :id="`${id}_numPeople`" type="number" :min="minPeople" :max="maxPeople" :placeholder="sprintf(__('Mínimo de %s pessoas', 'iande'), minPeople)" v-model.number="numPeople" :v="v.num_people"/>
        </div>
        <div>
            <Label :for="`${id}_ageRange`">{{ __('Perfil etário', 'iande') }}</Label>
            <Select :id="`${id}_ageRange`" v-model="ageRange" :v="v.age_range" :options="$iande.ageRanges"/>
        </div>
        <div>
            <Label :for="`${id}_scholarity`">{{ __('Escolaridade', 'iande') }}</Label>
            <Select :id="`${id}_scholarity`" v-model="scholarity" :v="v.scholarity" :options="$iande.scholarity"/>
        </div>
        <div>
            <Label :for="`${id}_numResponsible`">{{ __('Quantidade prevista de responsáveis', 'iande') }}</Label>
            <Input :id="`${id}_numResponsible`" type="number" min="1" :max="maxResponsible" :placeholder="sprintf(__('Mínimo de 1 e máximo de %s pessoas', 'iande'), maxResponsible)" v-model.number="numResponsible" :v="v.num_responsible"/>
        </div>
        <div>
            <Label :for="`${id}_otherLanguages`">{{ __('O grupo fala algum idioma diferente de português?', 'iande') }}</Label>
            <RadioGroup :id="`${id}_otherLanguages`" v-model="otherLanguages" :v="$v.otherLanguages" :options="binaryOptions"/>
        </div>
        <template v-if="otherLanguages">
            <Repeater :id="`${id}_languages`" v-model="languages" :factory="newLanguage" :v="v.languages">
                <template #item="{ id, onUpdate, v, value }">
                    <div :key="id">
                        <LanguageInfo :id="id" :value="value" :v="v" @updateValue="onUpdate"/>
                    </div>
                </template>
                <template #addItem="{ action }">
                    <div class="iande-add-item" role="button" tabindex="0" @click="action">
                        <span><Icon icon="circle-plus"/></span>
                        <div class="iande-label">{{ __('Adicionar idioma', 'iande') }}</div>
                    </div>
                </template>
            </Repeater>
        </template>
        <div>
            <Label :for="`${id}_haveDisabilities`">{{ __('Há pessoa com deficiência no grupo?', 'iande') }}</Label>
            <RadioGroup :id="`${id}_haveDisabilities`" v-model="haveDisabilities" :v="$v.haveDisabilities" :options="binaryOptions"/>
        </div>
        <template v-if="haveDisabilities">
            <Repeater :id="`${id}_disabilities`" v-model="disabilities" :factory="newDisability" :v="v.disabilities">
                <template #item="{ id, onUpdate, v, value }">
                    <div :key="id">
                        <DisabilityInfo :id="id" :value="value" :v="v" @updateValue="onUpdate"/>
                    </div>
                </template>
                <template #addItem="{ action }">
                    <div class="iande-add-item" role="button" tabindex="0" @click="action">
                        <span><Icon icon="circle-plus"/></span>
                        <div class="iande-label">{{ __('Adicionar deficiência', 'iande') }}</div>
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
    import Label from '@components/Label.vue'
    import LanguageInfo from '@components/LanguageInfo.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import Repeater from '@components/Repeater.vue'
    import Select from '@components/Select.vue'
    import CustomField from '@mixins/CustomField'
    import { __ } from '@plugins/wp-i18n'
    import { constant, subModel } from '@utils'

    export default {
        name: 'GroupAdditionalInfo',
        components: {
            DisabilityInfo,
            Input,
            Label,
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
            binaryOptions: constant({
                [__('Não', 'iande')]: false,
                [__('Sim', 'iande')]: true,
            }),
            disabilities: subModel('disabilities'),
            exhibition: get('appointments/exhibition'),
            languages: subModel('languages'),
            maxPeople () {
                return this.exhibition?.group_size ? Number(this.exhibition.group_size) : 100
            },
            maxResponsible () {
                return this.exhibition?.max_num_responsible ? Number(this.exhibition.max_num_responsible) : 2
            },
            minPeople () {
                return this.exhibition?.min_group_size ? Number(this.exhibition.min_group_size) : 5
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
                    if (this.disabilities && this.disabilities.length > 0) {
                        this.haveDisabilities = true
                    }
                },
                immediate: true
            },
            languages: {
                handler () {
                    if (this.languages && this.languages.length > 0) {
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
