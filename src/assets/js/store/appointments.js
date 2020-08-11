import { make } from 'vuex-pathify'

const state = {
    current: {
        date: '',
        hour: '',
        name: '',
        objective: '',
    }
}

export default {
    state,
    getters: make.getters(state),
    mutations: make.mutations(state),
    actions: make.actions(state),
    namespaced: true
}
