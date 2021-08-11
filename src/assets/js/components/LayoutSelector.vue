<template>
    <div class="iande-field">
        <div class="iande-layout-selector" :id="id" :class="fieldClass" role="radiogroup" :aria-describedby="errorId">
            <label v-for="(description, option) of options" :key="option">
                <input type="radio" :name="id" :value="option" v-model="modelValue">
                <span class="iande-layout-selector__name">{{ sprintf(__('Opção %s', 'iande'), option) }}</span>
                <img :src="`${$iande.iandePath}assets/img/itinerary-layout-0${option}.png`" alt="">
                <span class="iande-layout-selector__description">{{ description }}</span>
            </label>
        </div>
        <FormError :id="errorId" :v="v" v-if="v.$error"/>
    </div>
</template>

<script>
    import CustomField from '@mixins/CustomField'
    import { __ } from '@plugins/wp-i18n'
    import { constant } from '@utils'

    export default {
        name: 'LayoutSelector',
        mixins: [CustomField],
        computed: {
            options: constant({
                1: __('Indicado para roteiros com quantidade razoável de texto', 'iande'),
                2: __('Indicado para roteiros com pouco texto', 'iande'),
                3: __('Apresenta imagens grandes com os textos sobrepostos', 'iande'),
                4: __('Apresenta imagens grandes e apenas o título atrelado', 'iande'),
            }),
        },
    }
</script>
