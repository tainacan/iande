<template>
    <div>
        <article class="mt-lg">
            <div class="iande-container narrow iande-stack stack-lg">
                <h1 v-if="user">{{ sprintf(__('Olá, %s', 'iande'), user.first_name || '') }}</h1>
                <p v-html="__(`Boas vindas ao <b class='text-secondary'>iandé</b>, plataforma de agendamento e visitação à museus. Diga abaixo que tipo de visita você prentede realizar.`, 'iande')"/>

                <WelcomeOption :title="__('Ver opções de Roteiros Virtuais', 'iande')" :href="$iandeUrl('itinerary/list')" v-if="$iande.tainacanActive">
                    <p>{{ __('Visite roteiros virtuais feitos a partir do acervo digital do museu ou crie seus próprios roteiros!', 'iande') }}</p>
                </WelcomeOption>

                <WelcomeOption :title="__('Agendar uma Visita Presencial', 'iande')" :href="$iandeUrl('appointment/create')">
                    <p>{{ __('Agende uma visita presencial para você, seus amigos, seus estudantes, sua família ou quem você quiser.', 'iande') }}</p>
                </WelcomeOption>
            </div>
        </article>
    </div>
</template>

<script>
    import { get } from 'vuex-pathify'

    import WelcomeOption from '@components/WelcomeOption.vue'

    export default {
        name: 'WelcomePage',
        components: {
            WelcomeOption,
        },
        computed: {
            user: get('users/current'),
        },
    }
</script>