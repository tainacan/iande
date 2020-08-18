import { make } from 'vuex-pathify'

function newAppointment() {
    return {
        date: '',
        group_nature: '',
        hour: '',
        ID: null,
        institution: null,
        name: '',
        purpose: '',
        responsible_email: '',
        responsible_first_name: '',
        responsible_last_name: '',
        responsible_phone: '',
        responsible_role: '',
        step: 1
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
    mutations: {
        ...make.mutations(state),
        RESET (state) {
            state.current = newAppointment()
        }
    },
    actions: {
        ...make.actions(state),
        reset ({ commit }) {
            commit('RESET')
        }
    },
    namespaced: true
}
