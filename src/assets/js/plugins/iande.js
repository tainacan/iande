const $iande = window.IandeSettings

export default {
    install (Vue) {
        Vue.prototype.$iande = $iande
        Vue.prototype.$iandeUrl = (suffix = '') => `${$iande.iandeUrl}/${suffix}`

        if ($iande.recaptchaKey) {
            const location = window.location.href
            if (location.includes('user/login') || location.includes('user/create')) {
                import(/* webpackChunkName: 'vue-recaptcha-v3' */ 'vue-recaptcha-v3').then(({ VueReCaptcha }) => {
                    Vue.use(VueReCaptcha, { siteKey: $iande.recaptchaKey })
                })
            }
        }
    }
}
