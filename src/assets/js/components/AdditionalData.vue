<template>
    <div class="iande-stack stack-lg">
        <h1>Dados adicionais</h1>
        <div>
            <label for="hasVisitedPreviously" class="iande-label">Você já visitou o museu anteriormente?</label>
            <RadioGroup id="hasVisitedPreviously" v-model="hasVisitedPreviously" :validations="$v.hasVisitedPreviously" :options="binaryOptions"/>
        </div>
        <div>
            <label for="hasPreparedVisit" class="iande-label">Você preparou seu grupo para a visita?</label>
            <RadioGroup id="hasPreparedVisit" v-model="hasPreparedVisit" :validations="$v.hasPreparedVisit" :options="binaryOptions"/>
        </div>
        <div v-if="hasPreparedVisit">
            <label for="howPreparedVisit" class="iande-label">De que maneira você preparou o grupo?</label>
            <TextArea id="howPreparedVisit" placeholder="Quais conteúdos gostaria que fossem abordados na visita?" v-model="howPreparedVisit" :validations="$v.howPreparedVisit"/>
        </div>
        <div>
            <label for="additionalComment" class="iande-label">Deseja comentar algo mais?</label>
            <TextArea id="additionalComment" placeholder="Escreva aqui" v-model="additionalComment" :validations="$v.additionalComment"/>
        </div>
    </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import { sync } from 'vuex-pathify'

    import RadioGroup from './RadioGroup.vue'
    import TextArea from './TextArea.vue'
    import { constant } from '../utils'

    export default {
        name: 'AdditionalData',
        components: {
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
            binaryOptions: constant({ 'Sim': true, 'Não': false }),
        },
        validations: {
            additionalComment: { },
            hasPreparedVisit: { required },
            hasVisitedPreviously: { required },
            howPreparedVisit: { },
        }
    }
</script>