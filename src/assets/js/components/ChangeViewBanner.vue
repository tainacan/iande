<template>
    <div>
        <div class="iande-navbar-alert" v-if="!dismissed">
            <div class="iande-container">
                <div v-if="value === 'educator'">
                    {{ __('Você está na visualização de educador.', 'iande') }}
                    <a :href="$iandeUrl('user/welcome?force_view=visitor')">
                        {{ __('Alternar para a visualização de visitante', 'iande') }}
                    </a>
                </div>
                <div v-else>
                    {{ __('Você está na visualização de visitante.', 'iande') }}
                    <a :href="$iandeUrl('group/list?force_view=educator')">
                        {{ __('Alternar para visualização de educador', 'iande') }}
                    </a>
                </div>
                <a :aria-label="__('Fechar', 'iande')" role="button" href="javascript:void(0)" @click="dismiss" @keypress.enter="dismiss">
                    <Icon icon="xmark"/>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import { qs } from '@utils'

    export default {
        name: 'ChangeViewBanner',
        props: {
            value: { type: String, required: true },
        },
        model: {
            prop: 'value',
            event: 'updateValue',
        },
        data () {
            return {
                dismissed: false,
            }
        },
        beforeMount () {
            if (qs.has('force_view')) {
                const viewMode = qs.get('force_view')
                window.sessionStorage.setItem('view', viewMode);
                this.$emit('updateValue', viewMode)
            } else {
                const viewMode = window.sessionStorage.getItem('view')
                if (viewMode) {
                    this.$emit('updateValue', viewMode)
                }
            }

            if (window.sessionStorage.getItem('view_dismissed')) {
                this.dismissed = true
            }
        },
        methods: {
            dismiss () {
                window.sessionStorage.setItem('view_dismissed', '1')
                this.dismissed = true
            },
        }
    }
</script>
