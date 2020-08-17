import api, { searchParams } from './api'

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
