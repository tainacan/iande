import { make } from 'vuex-pathify'

const state = {
    list: []
}

export default {
    state,
    getters: {
        ...make.getters(state),
        default (state) {
            if (state.list.length > 0) {
                return state.list[0]
            } else {
                return null
            }
        }
    },
    mutations: make.mutations(state),
    actions: make.actions(state),
    namespaced: true
}
