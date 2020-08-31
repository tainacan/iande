import { make } from 'vuex-pathify'

function newAppointment() {
    return {
        additional_comment: '',
        date: '',
        group_list: {
            groups: [],
        },
        group_nature: '',
        has_prepared_visit: 'no',
        has_visited_previously: 'no',
        how_prepared_visit: '',
        hour: '',
        ID: null,
        institution_id: null,
        name: '',
        purpose: '',
        purpose_other: '',
        responsible_email: '',
        responsible_first_name: '',
        responsible_last_name: '',
        responsible_phone: '',
        responsible_role: '',
        responsible_role_other: '',
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
