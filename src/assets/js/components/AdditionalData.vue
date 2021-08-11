<template>
    <div class="iande-stack stack-lg">
        <h1>{{ __('Dados adicionais', 'iande') }}</h1>
        <div>
            <Label for="hasVisitedPreviously">{{ __('Você já visitou o museu anteriormente?', 'iande') }}</Label>
            <RadioGroup id="hasVisitedPreviously" v-model="hasVisitedPreviously" :v="$v.hasVisitedPreviously" :options="binaryOptions"/>
        </div>
        <div>
            <Label for="hasPreparedVisit">{{ __('Você preparou seu grupo para a visita?', 'iande') }}</Label>
            <RadioGroup id="hasPreparedVisit" v-model="hasPreparedVisit" :v="$v.hasPreparedVisit" :options="binaryOptions"/>
        </div>
        <div v-if="hasPreparedVisit === 'yes'">
            <Label for="howPreparedVisit">{{ __('De que maneira você preparou o grupo?', 'iande') }}</Label>
            <TextArea id="howPreparedVisit" placeholder="Quais conteúdos gostaria que fossem abordados na visita?" v-model="howPreparedVisit" :v="$v.howPreparedVisit"/>
        </div>
        <div>
            <Label for="additionalComment">{{ __('Deseja comentar algo mais?', 'iande') }}</Label>
            <TextArea id="additionalComment" placeholder="Escreva aqui" v-model="additionalComment" :v="$v.additionalComment"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import Label from '@components/Label.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import TextArea from '@components/TextArea.vue'
    import { __ } from '@plugins/wp-i18n'
    import { constant } from '@utils'

    export default {
        name: 'AdditionalData',
        components: {
            Label,
            RadioGroup,
            TextArea,
        },
        computed: {
            ...sync('appointments/current@', {
               additionalComment: 'additional_comment',
               hasPreparedVisit: 'has_prepared_visit',
               hasVisitedPreviously: 'has_visited_previously',
               howPreparedVisit: 'how_prepared_visit',
            }),
            binaryOptions: constant({
                [__('Não', 'iande')]: 'no',
                [__('Sim', 'iande')]: 'yes',
            }),
        },
        validations: {
            additionalComment: { },
            hasPreparedVisit: { required },
            hasVisitedPreviously: { required },
            howPreparedVisit: { },
        }
    }
</script>
