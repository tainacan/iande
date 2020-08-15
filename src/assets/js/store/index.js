import Vuex from 'vuex'
import pathify from 'vuex-pathify'
import createPersistedState from 'vuex-persistedstate'

import appointments from './appointments'
import institutions from './institutions'
import user from './user'

const persistence = createPersistedState({
    paths: [
        'appointments.list',
        'institutions.list',
        'user.user',
    ]
})

export default function createStore() {
    return new Vuex.Store({
        modules: {
            appointments,
            institutions,
            user,
        },
        plugins: [
            persistence,
            pathify.plugin,
        ]
    })
}
