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
        post_status: 'draft',
        profile: '',
        profile_other: '',
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
            state.current = newInstitution()
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
