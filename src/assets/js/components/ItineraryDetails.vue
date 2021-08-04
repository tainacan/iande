<template>
    <div class="iande-container narrow mt-lg">
        <form class="iande-form iande-stack stack-lg" @submit.prevent>
            <h1>{{ __('Configuração do roteiro', 'iande') }}</h1>

            <div>
                <label for="name" class="iande-label">{{ __('Título do roteiro', 'iande') }}</label>
                <Input id="name" type="text" :placeholder="__('Título do roteiro', 'iande')" v-model="itinerary.name" :validations="v.itinerary.name"/>
            </div>

            <div>
                <label for="cover" class="iande-label">{{ __('Imagem de capa', 'iande') }}</label>
                <FileUploader id="cover" accept="image/*" v-model="itinerary.cover" :validations="v.itinerary.cover"/>
            </div>

            <div>
                <label for="description" class="iande-label">{{ __('Descrição breve', 'iande') }}<span class="iande-label__optional">{{ __('(opcional)', 'iande') }}</span></label>
                <TextArea id="description" v-model="itinerary.description" :validations="v.itinerary.description"/>
            </div>

            <div>
                <label for="publicly_findable" class="iande-label">{{ __('O roteiro será aberto ao público ou restrito a quem tiver o link de acesso?', 'iande') }}</label>
                <RadioGroup id="publicly_findable" v-model="itinerary.publicly_findable" :options="publicOptions" :validations="v.itinerary.publicly_findable"/>
            </div>

            <div>
                <label for="shareable" class="iande-label">{{ __('Você deseja deixar seu roteiro compartilhável?', 'iande') }}</label>
                <RadioGroup id="shareable" v-model="itinerary.shareable" :options="shareOptions" :validations="v.itinerary.shareable"/>
            </div>

            <div>
                <label for="layout" class="iande-label">{{ __('Escolha o layout de visualização desktop do roteiro', 'iande') }}</label>
                <LayoutSelector id="layout" v-model="itinerary.layout" :validations="v.itinerary.layout"/>
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
