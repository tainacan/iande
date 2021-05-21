<template>
    <div>
        <div class="iande-navbar-alert" v-if="!dismissed">
            <div class="iande-container">
                <div v-if="value === 'educator'">
                    Você está vendo a visualização de educador.
                    <a role="button" href="javascript:void(0)" @click="setViewMode('visitor')" @keypress.enter="setViewMode('visitor')">
                        Alternar para a visualização de visitante
                    </a>
                </div>
                <div v-else>
                    Você está vendo a visualização de visitante.
                    <a role="button" href="javascript:void(0)" @click="setViewMode('educator')" @keypress.enter="setViewMode('educator')">
                        Alternar para visualização de educador
                    </a>
                </div>
                <a aria-label="Fechar" role="button" href="javascript:void(0)" @click="dismiss" @keypress.enter="dismiss">
                    <Icon icon="times"/>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
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
            const qs = new URLSearchParams(window.location.search)
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
            setViewMode (viewMode) {
                window.sessionStorage.setItem('view', viewMode)
                this.$emit('updateValue', viewMode)
            },
        }
    }
</script>
