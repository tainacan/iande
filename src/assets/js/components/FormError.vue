<template>
    <div class="iande-form-error" :id="id" v-if="v.$error">
        <span v-if="v.required === false">{{ __('Campo obrigatório', 'iande') }}</span>
        <span v-else-if="v.samePassword === false">{{ __('Senhas não batem', 'iande') }}</span>
        <span v-else-if="v.cep === false">{{ __('CEP inválido', 'iande') }}</span>
        <span v-else-if="v.cnpj === false">{{ __('CNPJ inválido', 'iande') }}</span>
        <span v-else-if="v.date === false">{{ __('Data inválida', 'iande') }}</span>
        <span v-else-if="v.email === false">{{ __('E-mail inválido', 'iande') }}</span>
        <span v-else-if="v.phone === false">{{ __('Telefone inválido', 'iande') }}</span>
        <span v-else-if="v.time === false">{{ __('Horário inválido', 'iande') }}</span>
        <span v-else-if="v.integer === false">{{ __('Valor não é número inteiro', 'iande') }}</span>
        <span v-else-if="v.maxLength === false">{{ sprintf(__('Selecione até %s opções', 'iande'), v.$params.maxLength.max) }}</span>
        <span v-else-if="v.maxValue === false">{{ sprintf(__('Valor máximo é %s', 'iande'), v.$params.maxValue.max) }}</span>
        <span v-else-if="v.minValue === false">{{ sprintf(__('Valor mínimo é %s', 'iande'), v.$params.minValue.min) }}</span>
        <span v-else-if="v.minChar === false">{{ sprintf(__('Campo tem que ter pelo menos %s caracteres', 'iande'), v.$params.minChar.min) }}</span>
        <span v-else-if="v.minGroups === false">{{ __('É necessário pelo menos um grupo', 'iande') }}</span>
    </div>
</template>

<script>
    export default {
        name: 'FormError',
        props: {
            id: { type: String, required: true },
            v: { type: Object, required: true },
        }
    }
</script>
