import Vuex from 'vuex'
import pathify from 'vuex-pathify'

import appointments from './appointments'
import exhibitions from './exhibitions'
import groups from './groups'
import institutions from './institutions'
import users from './users'

export default function createStore() {
    return new Vuex.Store({
        modules: {
            appointments,
            exhibitions,
            groups,
            institutions,
            users,
        },
        plugins: [
            pathify.plugin,
        ]
    })
}
