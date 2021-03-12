export default {
    install (Vue) {
        const i18n = window.wp.i18n
        if (i18n) {
            Vue.prototype.__ = i18n.__
            Vue.prototype._x = i18n._x
            Vue.prototype._n = i18n._n
            Vue.prototype._nx = i18n._nx
        } else {
            /* Very minimal polyfill */
            const id = (x) => x
            const sp = (singular, plural, n) => n > 1 ? plural.replace('%s', n) : singular.replace('%s', n)
            Vue.prototype.__ = id
            Vue.prototype._x = id
            Vue.prototype._n = sp
            Vue.prototype._nx = sp
        }
    }
}
