import { make } from 'vuex-pathify'

function newAppointment() {
    return {
        date: '',
        hour: '',
        name: '',
        objective: '',
    }
}

const state = {
    current: newAppointment()
}

export default {
    state,
    getters: make.getters(state),
    mutations: make.mutations(state),
    actions: make.actions(state),
    namespaced: true
}
