import { make } from 'vuex-pathify'

const state = {
    list: []
}

export default {
    state,
    getters: make.getters(state),
    mutations: make.mutations(state),
    actions: make.actions(state),
    namespaced: true
}
