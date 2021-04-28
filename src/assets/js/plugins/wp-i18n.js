export const i18n = window.wp.i18n

export default {
    install (Vue) {
        Vue.prototype.__ = i18n.__
        Vue.prototype._x = i18n._x
        Vue.prototype._n = i18n._n
        Vue.prototype._nx = i18n._nx
        Vue.prototype.sprintf = i18n.sprintf
    }
}
