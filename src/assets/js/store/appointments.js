import { make } from 'vuex-pathify'

function newAppointment() {
    return {
        date: '',
        hour: '',
        name: '',
        nature: '',
        purpose: '',
        responsible_first_name: '',
        responsible_last_name: '',
        responsible_email: '',
        responsible_phone: '',
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
