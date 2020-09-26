<template>
    <div class="iande-form-error" :id="id" v-if="validations.$error">
        <span v-if="validations.required === false">Campo obrigatório</span>
        <span v-else-if="validations.samePassword === false">Senhas não batem</span>
        <span v-else-if="validations.cep === false">CEP inválido</span>
        <span v-else-if="validations.cnpj === false">CNPJ inválido</span>
        <span v-else-if="validations.date === false">Data inválida</span>
        <span v-else-if="validations.email === false">E-mail inválido</span>
        <span v-else-if="validations.phone === false">Telefone inválido</span>
        <span v-else-if="validations.time === false">Horário inválido</span>
        <span v-else-if="validations.integer === false">Valor não é número inteiro</span>
        <span v-else-if="validations.maxValue === false">Valor máximo é {{ validations.$params.maxValue.max }}</span>
        <span v-else-if="validations.minChar === false">Campo tem que ter pelo menos {{ validations.$params.minChar.min }} caracteres</span>
        <span v-else-if="validations.minGroups === false">É necessário pelo menos um grupo</span>
    </div>
</template>

<script>
    export default {
        name: 'FormError',
        props: {
            id: { type: String, required: true },
            validations: { type: Object, required: true },
        }
    }
</script>