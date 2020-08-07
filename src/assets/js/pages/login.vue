<template>
    <div class="container stack-lg narrow">
        <h1>Boas vindas!</h1>
        <p class="slogan">Você está na plataforma de agendamento <span class="text-secondary">iandé</span> + {{ institution }}.</p>
        <p>Para agendar uma visita é simples. Basta você se logar e informar os dados solicitados nas 3 etapas a seguir:</p>
        <p>Após a confirmação, você receberá um email com o resumo sobre o agendamento. Vamos lá?</p>
        <form class="form stack-lg" @submit.prevent>
            <div>
                <div class="label">Faça login para começar:</div>
                <div class="grid">
                    <input class="input" type="email" placeholder="email" aria-label="E-mail" v-model="email">
                    <div>
                        <input class="input" type="password" placeholder="senha" aria-label="Senha" v-model="password">
                        <a class="link" href="#">Não lembro a senha</a>
                    </div>
                </div>
            </div>
            <div class="stack-md">
                <button class="button solid" type="submit">Fazer login</button>
                <a class="button outline" href="#">Criar login</a>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: {
            institution: { type: String, required: true },
        },
        data () {
            return {
                email: '',
                password: '',
            }
        }
    }
</script>