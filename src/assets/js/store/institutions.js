import { make } from 'vuex-pathify'

function newInstitution () {
    return {
        ID: null
    }
}

const state = {
    current: newInstitution(),
    list: []
}

export default {
    state,
    getters: make.getters(state),
    mutations: make.mutations(state),
    actions: make.actions(state),
    namespaced: true
}
