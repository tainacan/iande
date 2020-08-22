import Vuex from 'vuex'
import pathify from 'vuex-pathify'

import appointments from './appointments'
import institutions from './institutions'
import user from './user'

export default function createStore() {
    return new Vuex.Store({
        modules: {
            appointments,
            institutions,
            user,
        },
        plugins: [
            pathify.plugin,
        ]
    })
}
