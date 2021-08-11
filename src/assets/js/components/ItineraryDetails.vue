<template>
    <div class="iande-container narrow mt-lg">
        <form class="iande-form iande-stack stack-lg" @submit.prevent>
            <h1>{{ __('Configuração do roteiro', 'iande') }}</h1>

            <div>
                <Label for="name">{{ __('Título do roteiro', 'iande') }}</Label>
                <Input id="name" type="text" :placeholder="__('Título do roteiro', 'iande')" v-model="itinerary.name" :v="v.itinerary.name"/>
            </div>

            <div>
                <Label for="cover">{{ __('Imagem de capa', 'iande') }}</Label>
                <FileUploader id="cover" accept="image/*" v-model="itinerary.cover" :v="v.itinerary.cover"/>
            </div>

            <div>
                <Label for="description" :side="__('(opcional)', 'iande')">{{ __('Descrição breve', 'iande') }}</Label>
                <TextArea id="description" v-model="itinerary.description" :v="v.itinerary.description"/>
            </div>

            <div>
                <Label for="publicly_findable">{{ __('O roteiro será aberto ao público ou restrito a quem tiver o link de acesso?', 'iande') }}</Label>
                <RadioGroup id="publicly_findable" v-model="itinerary.publicly_findable" :options="publicOptions" :v="v.itinerary.publicly_findable"/>
            </div>

            <div>
                <Label for="shareable">{{ __('Você deseja deixar seu roteiro compartilhável?', 'iande') }}</Label>
                <RadioGroup id="shareable" v-model="itinerary.shareable" :options="shareOptions" :v="v.itinerary.shareable"/>
            </div>

            <div>
                <Label for="layout">{{ __('Escolha o layout de visualização desktop do roteiro', 'iande') }}</Label>
                <LayoutSelector id="layout" v-model="itinerary.layout" :v="v.itinerary.layout"/>
            </div>

            <div class="iande-form-error" v-if="formError">
                <span>{{ __(formError, 'iande') }}</span>
            </div>

            <div class="iande-form-grid">
                <a class="iande-button solid" :href="$iandeUrl('itinerary/list')">
                    {{ __('Cancelar', 'iande') }}
                </a>

                <button type="button" class="iande-button primary" @click="$emit('update')">
                    {{ __('Salvar alterações', 'iande') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import FileUploader from '@components/FileUploader.vue'
    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import LayoutSelector from '@components/LayoutSelector.vue'
    import RadioGroup from '@components/RadioGroup.vue'
    import TextArea from '@components/TextArea.vue'
    import { __ } from '@plugins/wp-i18n'
    import { constant } from '@utils'

    export default {
        name: 'ItineraryDetails',
        components: {
            FileUploader,
            Input,
            Label,
            LayoutSelector,
            RadioGroup,
            TextArea,
        },
        props: {
            formError: { type: String, default: '' },
            itinerary: { type: Object, required: true },
            v: { type: Object, required: true },
        },
        computed: {
            publicOptions: constant({
                [__('Aberto ao público', 'iande')]: 'yes',
                [__('Acesso apenas com link', 'iande')]: 'no',
            }),
            shareOptions: constant({
                [__('Não', 'iande')]: 'no',
                [__('Sim', 'iande')]: 'yes',
            }),
        },
    }
</script>
