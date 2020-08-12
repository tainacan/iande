import { sync } from 'vuex-pathify'

import api from './api'

export function constant (value) {
    return () => value
}

export function syncMany (path, keys) {
    let entries
    if (Array.isArray(keys)) {
        entries = keys.map(key => [key, sync(path.replace('*', key))])
    } else {
        entries = Object.entries(keys).map(([key, value]) => [key, sync(path.replace('*', value))])
    }
    return Object.fromEntries(entries)
}

export { api }
