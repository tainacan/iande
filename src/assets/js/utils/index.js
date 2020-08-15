import { sync } from 'vuex-pathify'

import api, { searchParams } from './api'

export function constant (value) {
    return () => value
}

export { api, searchParams }
