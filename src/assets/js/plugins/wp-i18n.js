export const i18n = window.wp.i18n
export const { __, _x, _n, _nx, sprintf } = i18n

export default {
    install (Vue) {
        Vue.prototype.__ = __
        Vue.prototype._x = _x
        Vue.prototype._n = _n
        Vue.prototype._nx = _nx
        Vue.prototype.sprintf = sprintf
    }
}
