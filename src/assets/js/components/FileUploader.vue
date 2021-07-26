<template>
    <div class="iande-field">
        <div class="iande-file-uploader">
            <input :id="id" :name="id" type="file" :aria-describedby="errorId" v-bind="$attrs" @change="upload">
        </div>
        <FormError :id="errorId" :validations="validations" v-if="validations.$error"/>
        <div class="iande-form-error" v-else-if="formError">
            <span>{{ __(formError, 'iande') }}</span>
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
            async upload (e) {
                const file = e.target.files[0]

                const formData = new FormData()
                formData.append('file', file)

                const res = window.fetch({

                })
            },
        },
    }
</script>
