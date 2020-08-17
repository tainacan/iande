import validateDate from 'validate-date'
import { helpers } from 'vuelidate/lib/validators'

export const cep = helpers.regex('cep', /^\d{8}$/)

export const cnpj = helpers.regex('cnpj', /^\d{14}$/)

export function date (value) {
    if (!value) {
        return true
    }
    return typeof value === 'string' && validateDate(value, 'boolean')
}

export const phone = helpers.regex('phone', /^\d{10,11}$/)

const timeRegex = /^([01][0-9]|2[0-3]):[0-5][0-9]$/
export function time (value) {
    if (!value) {
        return true
    }
    return typeof value === 'string' && Boolean(value.match(timeRegex))
}
