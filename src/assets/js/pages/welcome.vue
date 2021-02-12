<template>
    <div>
        <article class="mt-lg">
            <div class="iande-container narrow iande-stack stack-lg">
                <h1 v-if="user">Olá, {{ user.first_name }}</h1>
                <p>Boas vindas ao <b class="text-secondary">iandé</b>, plataforma de agendamento e visitação à museus. Diga abaixo que tipo de visita você prentede realizar.</p>

                <WelcomeOption title="Ver opções de Roteiros Virtuais">
                    <p>Visite roteiros virtuais feitos a partir do acervo digital do museu ou crie seus próprios roteiros!</p>
                </WelcomeOption>

                <WelcomeOption title="Agendar uma Visita Presencial" @select="createAppointment">
                    <p>Agende uma visita presencial para você, seus amigos, seus alunos, sua família ou quem você quiser.</p>
                </WelcomeOption>
            </div>
        </article>
        <AppointmentWelcomeModal ref="appointmentModal"/>
    </div>
</template>

<script>
    import { get } from 'vuex-pathify'

    import AppointmentWelcomeModal from '../components/AppointmentWelcomeModal.vue'
    import WelcomeOption from '../components/WelcomeOption.vue'

    export default {
        name: 'WelcomePage',
        components: {
            AppointmentWelcomeModal,
            WelcomeOption,
        },
        computed: {
            user: get('user/user'),
        },
        methods: {
            createAppointment () {
                this.$refs.appointmentModal.open()
            }
        },
    }
</script>