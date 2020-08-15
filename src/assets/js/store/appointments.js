import { make } from 'vuex-pathify'

function newAppointment() {
    return {
        date: '',
        hour: '',
        ID: null,
        name: '',
        nature: '',
        purpose: '',
        responsible_first_name: '',
        responsible_last_name: '',
        responsible_email: '',
        responsible_phone: '',
        responsible_role: '',
    }
}

const state = {
    current: newAppointment(),
    list: []
}

export default {
    state,
    getters: {
        ...make.getters(state),
        filteredFields (state) {
            const entries = Object.entries(state.current).filter(([key, value]) => {
                return value != null && value !== ''
            })
            return Object.fromEntries(entries)
        }
    },
    mutations: make.mutations(state),
    actions: make.actions(state),
    namespaced: true
}
