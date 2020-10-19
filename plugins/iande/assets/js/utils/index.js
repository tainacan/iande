import api, { searchParams } from './api'

export function constant (value) {
    return () => value
}

export function formatCep (cep) {
    return `${cep.slice(0, 5)}-${cep.slice(5, 8)}`
}

export function formatCnpj (cnpj) {
    return `${cnpj.slice(0, 8)}/${cnpj.slice(8, 12)}-${cnpj.slice(12, 14)}`
}

export function formatPhone (phone) {
    if (phone.length === 10) {
        return `(${phone.slice(0, 2)}) ${phone.slice(2, 6)}-${phone.slice(6, 10)}`
    } else {
        return `(${phone.slice(0, 2)}) ${phone.slice(2, 7)}-${phone.slice(7, 11)}`
    }
}

export function isOther (term) {
    return String(term).toLowerCase().includes('outr')
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

export function subModel (key) {
    return {
        get () {
            return this.modelValue[key]
        },
        set (newValue) {
            this.modelValue = { ...this.modelValue, [key]: newValue }
        }
    }
}

export function toArray (value) {
    if (!value) {
        return []
    } else if (Array.isArray(value)) {
        return value
    } else {
        return Object.values(value)
    }
}

export function watchForOther (vocabulary, other) {
    return function () {
        if (!isOther(this[vocabulary])) {
            this[other] = ''
        }
    }
}

export { api, searchParams }