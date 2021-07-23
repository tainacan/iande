let _qs = null

export default {
    get (key) {
        if (!_qs) {
            _qs = new URLSearchParams(window.location.search)
        }
        if (_qs.has(key)) {
            return _qs.get(key)
        } else {
            return null
        }
    },
    has (key) {
        if (!_qs) {
            _qs = new URLSearchParams(window.location.search)
        }
        return _qs.has(key)
    },
}
