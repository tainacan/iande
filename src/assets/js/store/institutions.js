import { make } from 'vuex-pathify'

function newInstitution () {
    return {
        address: '',
        address_number: '',
        city: '',
        cnpj: '',
        complement: '',
        district: '',
        email: '',
        ID: null,
        name: '',
        phone: '',
        profile: '',
        state: '',
        zip_code: ''
    }
}

const state = {
    current: newInstitution(),
    list: []
}

export default {
    state,
    getters: make.getters(state),
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
