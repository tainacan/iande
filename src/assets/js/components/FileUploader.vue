<template>
    <div class="iande-field">
        <div class="iande-file-uploader">
            <div>
                <div class="iande-file-uploader__thumbnail">
                    <img :src="value || `${this.$iande.iandePath}assets/img/cover-placeholder.png`" :alt="__('Preview', 'iande')">
                    <input ref="dialog" :id="id" :name="id" type="file" :aria-describedby="errorId" v-bind="$attrs" @change="upload">
                </div>
            </div>
            <div>
                <button type="button" class="iande-button small solid" @click="removeImage">
                    <Icon :icon="['far', 'trash-can']"/>
                    {{ __('Excluir imagem', 'iande') }}
                </button>
                <button type="button" class="iande-button small primary" @click="openDialog">
                    <Icon :icon="['far', 'image']"/>
                    {{ __('Enviar imagem', 'iande') }}
                </button>
                <FormError :id="errorId" :v="v" v-if="v.$error"/>
                <div class="iande-form-error" v-else-if="formError">
                    <span>{{ __(formError, 'iande') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CustomField from '@mixins/CustomField'

    export default {
        name: 'FileUploader',
        mixins: [CustomField],
        data () {
            return {
                formError: '',
            }
        },
        methods: {
            openDialog () {
                this.$refs.dialog.click()
            },
            removeImage () {
                this.$emit('updateValue', '')
            },
            async upload (e) {
                const file = e.target.files[0]

                const formData = new FormData()
                formData.append('file', file)

                const headers = new Headers()
                headers.set('X-WP-Nonce', this.$iande.nonce)

                try {
                    const res = await window.fetch(`${this.$iande.siteUrl}/wp-json/wp/v2/media`, {
                        method: 'POST',
                        headers,
                        body: formData,
                    })
                    const data = await res.json()
                    this.$emit('updateValue', data.source_url)
                } catch (err) {
                    this.formError = err.message || err
                }
            },
        },
    }
</script>
