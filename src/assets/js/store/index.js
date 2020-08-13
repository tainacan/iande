import Vuex from 'vuex'
import pathify from 'vuex-pathify'
import createPersistedState from 'vuex-persistedstate'

import appointments from './appointments'
import user from './user'

const persistence = createPersistedState({
    paths: ['user.user']
})

export default function createStore() {
    return new Vuex.Store({
        modules: {
            appointments,
            user,
        },
        plugins: [
            persistence,
            pathify.plugin,
        ]
    })
}
