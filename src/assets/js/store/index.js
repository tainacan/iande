import Vuex from 'vuex'
import pathify from 'vuex-pathify'

import appointments from './appointments'
import exhibitions from './exhibitions'
import institutions from './institutions'
import users from './users'

export default function createStore() {
    return new Vuex.Store({
        modules: {
            appointments,
            exhibitions,
            institutions,
            users,
        },
        plugins: [
            pathify.plugin,
        ]
    })
}
