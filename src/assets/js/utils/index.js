import { sync } from 'vuex-pathify'

import api from './api'

export function constant (value) {
    return () => value
}

export { api }
