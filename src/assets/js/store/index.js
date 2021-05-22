import Vuex from 'vuex'
import pathify from 'vuex-pathify'

import appointments from '@store/appointments'
import exhibitions from '@store/exhibitions'
import groups from '@store/groups'
import institutions from '@store/institutions'
import users from '@store/users'

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
