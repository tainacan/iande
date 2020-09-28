import { make } from 'vuex-pathify'

function newAppointment() {
    return {
        additional_comment: '',
        date: '',
        exhibition_id: null,
        group_nature: '',
        groups: [],
        has_prepared_visit: false,
        has_visited_previously: false,
        how_prepared_visit: '',
        hour: '',
        ID: null,
        institution_id: null,
        name: '',
        num_people: null,
        purpose: '',
        purpose_other: '',
        requested_exemption: false,
        responsible_email: '',
        responsible_first_name: '',
        responsible_last_name: '',
        responsible_phone: '',
        responsible_role: '',
        responsible_role_other: '',
        step: 1,
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
        exhibition (state, getters, rootState, rootGetters) {
            if (!state.current.exhibition_id) {
                return null
            }
            return rootGetters['exhibitions/list'].find(exhibition => exhibition.ID == state.current.exhibition_id)
        },
        filteredFields (state) {
            const entries = Object.entries(state.current).filter(([key, value]) => {
                if (key === 'groups' && Array.isArray(value)) {
                    return value.filter(prop => prop != null && prop != '')
                }
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
