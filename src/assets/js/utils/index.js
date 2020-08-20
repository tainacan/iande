import api, { searchParams } from './api'

export function formatCep (cep) {
    return `${cep.substr(0, 5)}-${cep.substr(5, 3)}`
}

export function formatPhone (phone) {
    if (phone.length === 10) {
        return `(${phone.substr(0, 2)}) ${phone.substr(2,4)}-${phone.substr(6,4)}`
    } else {
        return `(${phone.substr(0, 2)}) ${phone.substr(2,5)}-${phone.substr(7,4)}`
    }
}

export function constant (value) {
    return () => value
}

export function sortBy (fn, asc = true) {
    return (a, b) => {
        const newA = fn(a)
        const newB = fn(b)
        if (newA > newB) {
            return asc ? 1 : -1
        } else if (newA < newB) {
            return asc ? -1 : 1
        } else {
            return 0
        }
    }
}

export { api, searchParams }
