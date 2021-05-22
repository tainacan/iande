<template>
    <div class="iande-form-error" :id="id" v-if="validations.$error">
        <span v-if="validations.required === false">{{ __('Campo obrigatório', 'iande') }}</span>
        <span v-else-if="validations.samePassword === false">{{ __('Senhas não batem', 'iande') }}</span>
        <span v-else-if="validations.cep === false">{{ __('CEP inválido', 'iande') }}</span>
        <span v-else-if="validations.cnpj === false">{{ __('CNPJ inválido', 'iande') }}</span>
        <span v-else-if="validations.date === false">{{ __('Data inválida', 'iande') }}</span>
        <span v-else-if="validations.email === false">{{ __('E-mail inválido', 'iande') }}</span>
        <span v-else-if="validations.phone === false">{{ __('Telefone inválido', 'iande') }}</span>
        <span v-else-if="validations.time === false">{{ __('Horário inválido', 'iande') }}</span>
        <span v-else-if="validations.integer === false">{{ __('Valor não é número inteiro', 'iande') }}</span>
        <span v-else-if="validations.maxLength === false">{{ sprintf(__('Selecione até %s opções', 'iande'), validations.$params.maxLength.max) }}</span>
        <span v-else-if="validations.maxValue === false">{{ sprintf(__('Valor máximo é %s', 'iande'), validations.$params.maxValue.max) }}</span>
        <span v-else-if="validations.minValue === false">{{ sprintf(__('Valor mínimo é %s', 'iande'), validations.$params.minValue.min) }}</span>
        <span v-else-if="validations.minChar === false">{{ sprintf(__('Campo tem que ter pelo menos %s caracteres', 'iande'), validations.$params.minChar.min) }}</span>
        <span v-else-if="validations.minGroups === false">{{ __('É necessário pelo menos um grupo', 'iande') }}</span>
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
